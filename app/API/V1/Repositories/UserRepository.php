<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\Repositories\Repository;
use Doctrine\ORM\Query\Expr;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissionsContract;
use TempestTools\AclMiddleware\Repository\HasPermissionsQueryTrait;

/** @noinspection LongInheritanceChainInspection */
class UserRepository extends Repository implements RepoHasPermissionsContract
{
    use HasPermissionsQueryTrait;

	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = User::class;

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        $expr = new Expr();
        /** @noinspection NullPointerExceptionInspection */
        return [
            'default'=>[
                'read'=>[
                    'select'=>[
                        'standardSelect'=>'partial u.{id, name, address, job}'
                    ],
                    'where'=>[
                        'onlyCurrentUser'=>[
                            'type'=>'and',
                            'value'=>$expr->eq('u.id', $this->getArrayHelper()->parseArrayPath(['userEntity', 'id']))
                        ]
                    ]
                ],
                'permissions'=>[
                    'where'=>[
                        'fields'=>[
                            'password'=>[
                                'permissive'=>false
                            ]
                        ]
                    ]
                ]
            ],
            'user'=>[
                'extends'=>[':default']
            ],
            'admin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'where'=>[
                        'onlyCurrentUser'=>null
                    ]
                ],
                'permissions'=>[
                    'where'=>[
                        'fields'=>[
                            'password'=>[
                                'permissive'=>true
                            ]
                        ]
                    ]
                ]
            ],
            'testing'=>[]
        ];
    }
}