<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\PasswordReset;
use App\API\V1\Entities\User;
use App\Repositories\Repository;
use App\Exceptions\PasswordResetException;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;

/** @noinspection LongInheritanceChainInspection */
class PasswordResetRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = PasswordReset::class;

    /**
     * @param GenericEventArgs $e
     * @throws PasswordResetException
     */
    public function preCreate(GenericEventArgs $e): void
    {
        $k = $e->getArgs();
        $options = $k['frontEndOptions'];

        if (!isset($options['email'])) {
            throw PasswordResetException::noEmail();
        }

        /** @var UserRepository $roleRepo */
        $roleRepo = $this->getEm()->getRepository(User::class);
        $user = $roleRepo->findOneBy(["email" => $options['email']]);

        if (!$user) {
            throw PasswordResetException::noUserAssociatedEmail();
        }

        /** check verified email **/
        if (!$user->getEmailVerification()->getVerified()) {
            throw PasswordResetException::emailNotVerified();
        }

        /** check if has role **/
        if (!$user->hasRole('user')) {
            throw PasswordResetException::noRole();
        }
    }

    public function postCreate(GenericEventArgs $e): void
    {
        $k = $e->getArgs();
        /** @var PasswordReset $entity */
        $entity = $k['lastResult'] ?? null;
        if ($entity !== null) {
            $options = $k['frontEndOptions'];
            /** @var UserRepository $roleRepo */
            $roleRepo = $this->getEm()->getRepository(User::class);
            /** @var User $user */
            $user = $roleRepo->findOneBy(["email" => $options['email']]);
            $entity->setUser($user);
        }
    }

    /**
     * @param GenericEventArgs $e
     * @throws PasswordResetException
     */
    public function preUpdate(GenericEventArgs $e): void
    {
        $k = $e->getArgs();
        $params = $k['params'];
        /** @var PasswordReset $entity */
        $entity = $k['entity'];

        /** check verified email **/
        if (!$entity->getUser()->getEmailVerification()->getVerified()) {
            throw PasswordResetException::emailNotVerified();
        }

        /** check if has role **/
        if (!$entity->getUser()->hasRole('user')) {
            throw PasswordResetException::noRole();
        }

        /** check setting verified to false **/
        if ($params['verified'] === false) {
            throw PasswordResetException::cantSetFalse();
        }

        /** check already verified **/
        if( $params['verified'] === true && $entity->getVerified() === true) {
            throw PasswordResetException::alreadyVerified();
        }

        /** check no password value **/
        if (!isset($k['frontEndOptions']['password'])) {
            throw PasswordResetException::noPassword();
        }
    }

    /**
     * After a verification token is verified, it's user should be given the user role
     *
     * @param GenericEventArgs $e
     * @throws PasswordResetException
     */
    public function postUpdate(GenericEventArgs $e): void
    {
        $k = $e->getArgs();
        /**
         * @var PasswordReset $entity
         */
        $entity = $e->getArgs()['lastResult'] ?? null;

        // Set the associated user entity password to be the password passed from the front end in options
        if ($entity !== null && $entity->getBindParams()['verified'] === true) {
            if (!isset($k['frontEndOptions']['password'])) {
                throw PasswordResetException::noPassword();
            }
            $user = $entity->getUser();
            $user->setPassword($k['frontEndOptions']['password']);
        }
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'read'=>[
                    'permissions'=>[
                        'allowed'=>false
                    ]
                ]
            ],
            'guest'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ],
                    'query'=>[
                        'select'=>[
                            'tokenAndUser'=>'e, partial u.{id, name, address, job, locale, createdAt, updatedAt}'
                        ],
                        'innerJoin'=>[
                            'user'=>[
                                'join'=>'e.user',
                                'alias'=>'u',
                            ]
                        ]
                    ]
                ]
            ],
            'user'=>[
                'extends'=>[':guest']
            ],
            'admin'=>[
                'extends'=>[':guest']
            ],

        ];
    }

}