<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\API\V1\Entities\Permission;
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
     */
    public function createEmailVerificationCode(User $user): EmailVerification
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
            $conn->commit(); /* i do not know why this one needs 2 $conn->commit()s */
        } catch (\Exception $e) {
            $conn->rollBack();
        }

        return $emailVerification;
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
            'superAdmin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ]
                ]
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }

}