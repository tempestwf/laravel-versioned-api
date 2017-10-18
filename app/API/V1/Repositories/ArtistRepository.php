<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Artist;
use App\Repositories\Repository;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Crud\Doctrine\Events\GenericEventArgs;
use Doctrine\ORM\Query\Expr;

/** @noinspection LongInheritanceChainInspection */


/**
 * ArtistRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class ArtistRepository extends Repository
{
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = Artist::class;



    public function preStart(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $array = $this->getArrayHelper()->getArray();
        if (!isset($array['repoEvents'])) {
            $array['repoEvents'] = [];
        }
        $array['repoEvents']['preStart'] = $e->getArgs()->getArrayCopy();
    }

    public function preStop(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preStop']=$e;
    }

    public function preCreateBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preCreateBatch']=$e;
    }

    public function preCreate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preCreate']=$e;
    }

    public function validateCreate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['validateCreate']=$e;
    }

    public function verifyCreate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['verifyCreate']=$e;
    }

    public function processResultsCreate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['processResultsCreate']=$e;
    }

    public function postCreate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postCreate']=$e;
    }

    public function postCreateBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postCreateBatch']=$e;
    }


    public function preUpdateBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preUpdateBatch']=$e;
    }

    public function preUpdate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preUpdate']=$e;
    }

    public function validateUpdate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['validateUpdate']=$e;
    }

    public function verifyUpdate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['verifyUpdate']=$e;
    }

    public function processResultsUpdate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['processResultsUpdate']=$e;
    }

    public function postUpdate(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postUpdate']=$e;
    }

    public function postUpdateBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postUpdateBatch']=$e;
    }


    public function preDeleteBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preDeleteBatch']=$e;
    }

    public function preDelete(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preDelete']=$e;
    }

    public function validateDelete(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['validateDelete']=$e;
    }

    public function verifyDelete(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['verifyDelete']=$e;
    }

    public function processResultsDelete(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['processResultsDelete']=$e;
    }

    public function postDelete(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postDelete']=$e;
    }

    public function postDeleteBatch(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postDeleteBatch']=$e;
    }


    public function preRead(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['preRead']=$e;
    }

    public function validateRead(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['validateRead']=$e;
    }

    public function verifyRead(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['verifyRead']=$e;
    }

    public function processResultsRead(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['processResultsRead']=$e;
    }

    public function postRead(GenericEventArgs $e) {
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['repoEvents']['postRead']=$e;
    }



    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        $expr = new Expr();
        $arrayCache = new ArrayCache();
        /** @noinspection NullPointerExceptionInspection */
        $this->getArrayHelper()->getArray()['arrayCache'] = $arrayCache;

        /** @noinspection PhpUnusedLocalVariableInspection
         * @return array
         * @internal param $extra
         * @internal param $target
         */
        $mutate = function () {
          return ['testKey', ['testValue']];
        };
        /** @noinspection PhpUnusedLocalVariableInspection
         * @param $extra
         * @return bool
         * @internal param $target
         */
        $closure = function ($extra) {
            return !($extra['key'] === 'testKey' && $extra['settings'] === ['testValue']);
        };
        return [
            'default'=>[],
            'guest'=>[
                'extends'=>[':default']
            ],
            'user'=>[
                'extends'=>[':guest']
            ],
            // Below here is for testing purposes only
            'userArtistWithAlbums'=>[
                'extends'=>[':default'],
                'read'=>[
                    'query'=>[
                        'select'=>[
                            'artistsAndAlbums'=>'a, a2'
                        ],
                        'innerJoin'=>[
                            'justCurrentUsersAlbums'=>[
                                'join'=>'a.albums',
                                'alias'=>'a2',
                            ]
                        ]
                    ]
                ]
            ],
            'testTopLevelMutateAndClosure'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'settings'=>[
                            'mutate'=>ArrayExpressionBuilder::closure(function () {
                                return [null, ['iAm'=>'a mutant']];
                            }),
                            'closure'=>ArrayExpressionBuilder::closure(function ($extra) {
                                return !$extra['settings']['iAm'] === 'a mutant';
                            })
                        ]
                    ]
                ]
            ],
            'testQuery'=>[
                'extends'=>[':default'],
                'read'=>[
                    'query'=>[
                        'select'=>[
                            'artistsWithCustomAlias'=>'t',
                            'innerJoinTest'=>'a',
                        ],
                        'from'=>[
                            'fromTest'=>[
                                'className'=>Artist::class,
                                'alias'=>'t',
                                'indexBy'=>null,
                            ]
                        ],
                        'where'=>[
                            'exprArrayTest1'=>[
                                'value'=>[
                                    'expr'=>'orX',
                                    'arguments'=>[
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[1,1]
                                        ],
                                        [
                                            'expr'=>'neq',
                                            'arguments'=>[0,1]
                                        ],
                                        [
                                            'expr'=>'lt',
                                            'arguments'=>[0,1]
                                        ],
                                        [
                                            'expr'=>'lte',
                                            'arguments'=>[0,1]
                                        ],
                                        [
                                            'expr'=>'gt',
                                            'arguments'=>[1,0]
                                        ],
                                        [
                                            'expr'=>'gte',
                                            'arguments'=>[1,0]
                                        ],
                                        [
                                            'expr'=>'in',
                                            'arguments'=>['t.id',[1,0]]
                                        ],
                                        [
                                            'expr'=>'notIn',
                                            'arguments'=>['t.id',[1,0]]
                                        ],
                                        [
                                            'expr'=>'isNull',
                                            'arguments'=>['t.id']
                                        ],
                                        [
                                            'expr'=>'isNotNull',
                                            'arguments'=>['t.id']
                                        ],
                                        [
                                            'expr'=>'like',
                                            'arguments'=>['t.name', $expr->literal('%BEE%')]
                                        ],
                                        [
                                            'expr'=>'notLike',
                                            'arguments'=>['t.name', $expr->literal('%VAN%')]
                                        ],
                                        [
                                            'expr'=>'between',
                                            'arguments'=>['t.id',0,2]
                                        ]
                                    ]
                                ]
                            ],
                            'exprArrayTest2'=>[
                                'type'=>'and',
                                'value'=>[
                                    'expr'=>'eq',
                                    'arguments'=>[
                                        1, 1
                                    ]
                                ]
                            ],
                            'exprArrayTest3'=>[
                                'type'=>'or',
                                'value'=>$expr->eq(1, 1)
                            ],
                        ],
                        'having'=>[
                            'havingTest1'=>[
                                'value'=>$expr->eq(1, 1)
                            ],
                            'havingTest2'=>[
                                'type'=>'and',
                                'value'=>$expr->eq(1, 1)
                            ],
                            'havingTest3'=>[
                                'type'=>'or',
                                'value'=>$expr->eq(1, 1)
                            ]
                        ],
                        'innerJoin'=>[
                            'innerJoinTest'=>[
                                'join'=>'t.albums',
                                'alias'=>'a',
                                'conditionType'=>Expr\Join::WITH,
                                'condition'=>$expr->eq(1, 1),
                                'indexBy'=>null,
                            ]
                        ],
                        'leftJoin'=>[
                            'leftJoinTest'=>[
                                'join'=>'t.albums',
                                'alias'=>'a2',
                                'conditionType'=>Expr\Join::WITH,
                                'condition'=>$expr->eq(1, 1),
                                'indexBy'=>null,
                            ]
                        ],
                        'orderBy'=>[
                            'testOrderBy'=>[
                                'sort'=>'t.id',
                                'order'=>'DESC'
                            ]
                        ],
                        'groupBy'=>[
                            'groupByTest'=>'t.name'
                        ],
                    ],
                    'settings'=>[
                        'cache'=>[
                            'useQueryCache'=>false,
                            'useResultCache'=>true,
                            'timeToLive'=>777,
                            'cacheId'=>'test_cache_id',
                            'queryCacheDrive' => $arrayCache,
                            'resultCacheDriver' => $arrayCache
                        ],
                        'placeholders'=>[
                            'placeholderTest'=>[
                                'value'=>'some stuff2',
                            ]
                        ],
                        'fetchJoin'=>true
                    ]
                ],

            ],
            'testQuery2'=>[
                'extends'=>[':testQuery'],
                'read'=>[
                    'query'=>[
                        'where'=>[
                            'exprArrayTest1'=>null,
                            'exprArrayTest2'=>null,
                            'exprArrayTest3'=>null,
                            'simpleTest'=>[
                                'type'=>'and',
                                'value'=>$expr->eq(1, 1)
                            ],
                            'andPlaceholders'=>[
                                'type'=>'and',
                                'value'=>[
                                    'expr'=>'andX',
                                    'arguments'=>[
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[':placeholderTest2',$expr->literal('some stuff')]
                                        ],
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[':placeholderTest',$expr->literal('some stuff2')]
                                        ],
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[':frontEndTestPlaceholder',777]
                                        ],
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[':frontEndTestPlaceholder2',$expr->literal('stuff2')]
                                        ],
                                        [
                                            'expr'=>'eq',
                                            'arguments'=>[':placeholderTest3',$expr->literal('some stuff3')]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        'having'=>[
                            'havingTest1'=>null,
                            'havingTest2'=>null
                        ],
                        'groupBy'=>[
                            'groupByTest'=>null
                        ],
                        'orderBy'=>[
                            'testOrderBy'=>null
                        ],
                    ],
                ],
            ],
            'testSqlQuery'=>[
                'extends'=>[':testQuery2'],
                'read'=>[
                    'query'=>[
                        'select'=>[
                            'artistsWithCustomAlias'=>null,
                            'innerJoinTest'=>null,
                            'selectAll'=>'*'
                        ],
                        'from'=>[
                            'fromTest'=>[
                                'className'=>'artists',
                                'alias'=>'t',
                                'indexBy'=>null,
                            ]
                        ],
                        'innerJoin'=>[
                            'innerJoinTest'=>[
                                'join'=>'t.albums',
                                'alias'=>'a',
                                'conditionType'=>Expr\Join::WITH,
                                'condition'=>'a.artist_id = t.id',
                                'indexBy'=>null,
                            ]
                        ],
                        'leftJoin'=>[
                            'leftJoinTest'=>[
                                'join'=>'t.albums',
                                'alias'=>'a2',
                                'conditionType'=>Expr\Join::WITH,
                                'condition'=>'a.artist_id = t.id',
                                'indexBy'=>null,
                            ]
                        ],
                    ],
                    'settings'=>[
                        'queryType'=>'sql',
                        'cache'=>[
                            'queryCacheProfile'=>new QueryCacheProfile(0, 'some key')
                        ]
                    ]
                ]
            ],
            'testSqlQueryNoCache'=>[
                'extends'=>[':testSqlQuery'],
                'read'=>[
                    'settings'=>[
                        'queryType'=>'sql',
                        'cache'=>[
                            'queryCacheProfile'=>null
                        ]
                    ]
                ]
            ],
            'testAllowed'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>false
                    ]
                ]
            ],
            'testPermissions1'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'where'=>[
                            'permissive'=>true,
                            'fields'=>[
                                't.name'=>[
                                    'operators'=>[
                                        'eq'=>false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'testPermissions2'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'where'=>[
                            'permissive'=>true,
                            'fields'=>[
                                't.id'=>[
                                    'permissive'=>false
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'testPermissions3'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'where'=>[
                            'permissive'=>false,
                            'fields'=>[
                                't.name'=>[
                                    'permissive'=>true,
                                ],
                                't.id'=>[
                                    'permissive'=>true,
                                    'operators'=>[
                                        'gt'=>false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'testNonWherePermissions'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'having'=>[
                            'fields'=>[
                                't.name'=>[
                                    'operators'=>[
                                        'eq'=>false
                                    ]
                                ],
                            ]
                        ],
                        'orderBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'directions'=>[
                                        'ASC'=>false
                                    ]
                                ],
                            ]
                        ],
                        'groupBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'allowed'=>false
                                ],
                            ]
                        ],
                        'placeholders'=>[
                            'placeholderNames'=>[
                                'test'=>[
                                    'allowed'=>false
                                ],
                            ]
                        ],

                    ]
                ]
            ],
            'testPermissiveAllowPermissions'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'groupBy'=>[
                            'permissive'=>false
                        ],
                        'placeholders'=>[
                            'permissive'=>false,
                            'placeholderNames'=>[
                                'placeholderTest2'=>[
                                    'allowed'=>true
                                ]
                            ]
                        ],
                    ]
                ]
            ],
            'testMutateAndClosure'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'where'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>$mutate,
                                        'closure'=>$closure,
                                    ]
                                ],
                            ]
                        ],
                        'having'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>$mutate,
                                        'closure'=>$closure,
                                    ]
                                ],
                            ]
                        ],
                        'orderBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>$mutate,
                                        'closure'=>$closure,
                                    ]
                                ],
                            ]
                        ],
                        'groupBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>$mutate,
                                        'closure'=>$closure,
                                    ]
                                ]
                            ]
                        ],
                        'placeholders'=>[
                            'placeholderNames'=>[
                                'test'=>[
                                    'settings'=>[
                                        'mutate'=>$mutate,
                                        'closure'=>$closure,
                                    ]
                                ],
                            ]
                        ],
                    ]
                ]
            ],
            'testMutateUsed'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'where'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>function ($extra) {
                                            $newCondition = $extra['settings'];
                                            $newCondition['arguments'][0] .= ' Mutated';
                                            return ['notUsed', $newCondition];
                                        },
                                    ]
                                ],
                            ]
                        ],
                        'having'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>function ($extra) {
                                            $newCondition = $extra['settings'];
                                            $newCondition['arguments'][0] .= ' Mutated';
                                            return ['notUsed', $newCondition];
                                        },
                                    ]
                                ],
                            ]
                        ],
                        'orderBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>function ($extra) {
                                            return [$extra['key'].'Mutated', $extra['settings'].' Mutated'];
                                        },
                                    ]
                                ],
                            ]
                        ],
                        'groupBy'=>[
                            'fields'=>[
                                't.name'=>[
                                    'settings'=>[
                                        'mutate'=>function ($extra) {
                                            return [$extra['key'].'Mutated', 'not used'];
                                        },
                                    ]
                                ]
                            ]
                        ],
                        'placeholders'=>[
                            'placeholderNames'=>[
                                'test'=>[
                                    'settings'=>[
                                        'mutate'=>function ($extra) {
                                            $newCondition = $extra['settings'];
                                            $newCondition['value'] .= ' Mutated';
                                            return [$extra['key'].'Mutated', $newCondition];
                                        },
                                    ]
                                ],
                            ]
                        ],

                    ]
                ]
            ],
            'testTurnOffPrePopulate'=>[
                'create'=>[
                    'prePopulateEntities'=>false
                ],
                'update'=>[
                    'prePopulateEntities'=>false
                ],
                'delete'=>[
                    'prePopulateEntities'=>false
                ]
            ],
            'testing'=>[]
        ];
    }
}
