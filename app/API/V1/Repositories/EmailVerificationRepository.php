<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\API\V1\Entities\EmailVerification;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class EmailVerificationRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = EmailVerification::class;

    /**
     * @param User $user
     * @return EmailVerification
     * @throws \Doctrine\DBAL\ConnectionException
     * Deprecated
     */
    /*public function createEmailVerificationCode(User $user): EmailVerification
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        $emailVerification = null;
        try {
            $verification_code = str_random(env('AUTH_PASSWORD_LENGTH', 30));
            $emailVerification = new EmailVerification();
            $emailVerification->setVerificationCode($verification_code);
            $emailVerification->setUser($user);
            $em->persist($emailVerification);
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }

        return $emailVerification;
    }*/

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
                    // TODO: Write test to make sure you can read the list and also an individual one
                    'query'=>[
                        'select'=>[
                            'tokenAndUser'=>'e, u'
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
        ];
    }

}