<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Role;
use App\API\V1\Entities\EmailVerification;
use App\Exceptions\EmailVerificationException;
use App\Repositories\Repository;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;

/** @noinspection LongInheritanceChainInspection */
class EmailVerificationRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = EmailVerification::class;

    /**
     * After a verification token is verified, it's user should be given the user role
     *
     * @param GenericEventArgs $e
     * @throws \Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function postUpdate(GenericEventArgs $e): void
    {
        $entity = $e->getArgs()['lastResult'] ?? null;
        /**
         * @var $roleRepo RoleRepository
         */
        $roleRepo = $this->getEm()->getRepository(Role::class);

        /** check token expiration **/
        $date = $entity->getCreatedAt()->modify('+' . env('EMAIL_VERIFICATION_TOKEN_LIFE_SPAN', 1440) . ' minutes');
        if ($date < new \DateTime()) {
            throw EmailVerificationException::tokenExpired();
        }

        // Look at the entity that are passed and make each one have the user role
        if ($entity !== null) {
            /**
             * @var $entity EmailVerification
             */
            $verified = $entity->getBindParams()['verified'] ?? false;
            if ($verified === true) {
                $user = $entity->getUser();
                $roleRepo->addUserRoles($user, $roles = ['user'], false);
            }
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
                            'tokenAndUser'=>'e, partial u.{id, firstName, middleInitial, lastName, locale, address, job, locale, createdAt, updatedAt}'
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