<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\SocializeUser;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class SocializeUserRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = SocializeUser::class;

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
            ]
        ];
    }
}