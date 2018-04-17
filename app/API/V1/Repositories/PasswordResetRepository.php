<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\PasswordReset;
use App\Repositories\Repository;
use TempestTools\Common\Exceptions\Utility\PasswordResetException;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;

/** @noinspection LongInheritanceChainInspection */
class PasswordResetRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = PasswordReset::class;

    /**
     * After a verification token is verified, it's user should be given the user role
     *
     * @param GenericEventArgs $e
     * @throws \TempestTools\Common\Exceptions\Utility\PasswordResetException
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
            if (isset($k['frontEndOptions']['password']) === false) {
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