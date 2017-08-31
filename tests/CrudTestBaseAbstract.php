<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\AlbumRepository;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use Doctrine\ORM\Query;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Helper\ArrayHelper;
use TempestTools\Crud\Constants\EntityEventsConstants;
use TempestTools\Crud\Constants\RepositoryEventsConstants;
use TempestTools\Crud\Exceptions\Orm\EntityException;
use TempestTools\Crud\Exceptions\Orm\Helper\DataBindHelperException;
use TempestTools\Crud\Exceptions\Orm\Helper\EntityArrayHelperException;
use TempestTools\Crud\Exceptions\Orm\Helper\QueryBuilderHelperException;
use TempestTools\Crud\Exceptions\Orm\Wrapper\QueryBuilderWrapperException;

/** @noinspection LongInheritanceChainInspection */
abstract class CrudTestBaseAbstract extends TestCase
{
    use MakeEmTrait;

    public function createRobAndBobData():array
    {
        return [
            [
                'name'=>'bob',
                'email'=>'bob@bob.com',
                'password'=>'bobsyouruncle'
            ],
            [
                'name'=>'rob',
                'email'=>'rob@rob.com',
                'password'=>'norobsyouruncle'
            ],
        ];
    }


    /**
     * @return array
     */
    public function createData (): array
    {
        return [
            [
                'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                'releaseDate'=>new \DateTime('now'),
                'artist'=>[
                    'create'=>[
                        [
                            'name'=>'BEETHOVEN',
                            'assignType'=>'set',
                        ],
                    ],
                ],
                'users'=>[
                    'read'=>[
                        '1'=>[
                            'assignType'=>'addSingle',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $userIds
     * @return array
     */
    public function createArtistChainData (array $userIds):array {
        return [
            [
                'name'=>'BEETHOVEN',
                'albums'=>[
                    'create'=> [
                        [
                            'name'=> 'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                            'assignType'=>'addSingle',
                            'releaseDate'=>new \DateTime('now'),
                            'users'=>[
                                'read'=> [
                                    $userIds[0]=>[
                                        'assignType'=>'addSingle',
                                    ],
                                    $userIds[1]=>[
                                        'assignType'=>'addSingle',
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'=> 'BEETHOVEN: THE COMPLETE STRING QUARTETS',
                            'assignType'=>'addSingle',
                            'releaseDate'=>new \DateTime('now'),
                            'users'=>[
                                'read'=> [
                                    $userIds[0]=>[
                                        'assignType'=>'addSingle',
                                    ],
                                    $userIds[1]=>[
                                        'assignType'=>'addSingle',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name'=>'BACH',
                'albums'=>[
                    'create'=> [
                        [
                            'name'=> 'Amsterdam Baroque Orchestra',
                            'assignType'=>'addSingle',
                            'releaseDate'=>new \DateTime('now'),
                            'users'=>[
                                'read'=> [
                                    $userIds[0]=>[
                                        'assignType'=>'addSingle',
                                    ],
                                    $userIds[1]=>[
                                        'assignType'=>'addSingle',
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'=> 'The English Suites',
                            'assignType'=>'addSingle',
                            'releaseDate'=>new \DateTime('now'),
                            'users'=>[
                                'read'=> [
                                    $userIds[0]=>[
                                        'assignType'=>'addSingle',
                                    ],
                                    $userIds[1]=>[
                                        'assignType'=>'addSingle',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return ArrayHelper
     */
    public function makeArrayHelper ():ArrayHelper {
        /** @var User $repo */
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['id'=>1]);
        $arrayHelper = new ArrayHelper();
        $arrayHelper->extract([$user]);
        return $arrayHelper;
    }

    /**
     * @return array
     */
    protected function makeFrontEndQueryOptions():array
    {
        return [
            'returnCount'=>true,
            'limit'=>1,
            'offset'=>1,
        ];
    }
    /**
     * @return array
     */
    protected function makeTestFrontEndQueryArtist(): array
    {
        return [
            'query'=>[
                'where'=>[
                    [
                        'operator'=>'andX',
                        'conditions'=>[
                            [
                                'field'=>'t.name',
                                'operator'=>'eq',
                                'arguments'=>['BEETHOVEN1']
                            ],
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['Bob Marley']
                            ]
                        ]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN3']
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN4']
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'neq',
                        'arguments'=>['Blink 182']
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'and',
                        'operator'=>'lt',
                        'arguments'=>[99999991]
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'and',
                        'operator'=>'lte',
                        'arguments'=>[99999992]
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'and',
                        'operator'=>'gt',
                        'arguments'=>[-1]
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'and',
                        'operator'=>'gte',
                        'arguments'=>[-2]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'in',
                        'arguments'=>[['BEETHOVEN5']]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'notIn',
                        'arguments'=>[['Vanilla Ice']]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'isNull',
                        'arguments'=>[]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'isNotNull',
                        'arguments'=>[]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'like',
                        'arguments'=>['%BEETHOV%']
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'notLike',
                        'arguments'=>['%The Ruttles%']
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'and',
                        'operator'=>'between',
                        'arguments'=>[0,99999993]
                    ],
                    [
                        'type'=>'or',
                        'operator'=>'orX',
                        'conditions'=>[
                            [
                                'field'=>'t.name',
                                'operator'=>'eq',
                                'arguments'=>['BEETHOVEN2']
                            ],
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['Urethra Franklin']
                            ]
                        ]
                    ],
                ],
                'having'=>[
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN7']
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'or',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN6']
                    ],
                ],
                'orderBy'=>[
                    't.name'=>'ASC',
                    't.id'=>'DESC'
                ],
                'groupBy'=>[
                    't.name',
                    't.id'
                ],
                'placeholders'=>[
                    'frontEndTestPlaceholder'=>[
                        'value'=>777,
                        'type'=>'integer'
                    ],
                    'frontEndTestPlaceholder2'=>[
                        'value'=>'stuff2',
                        'type'=>'string'
                    ]
                ]
            ]
        ];
    }





    /**
     * @return array
     */
    protected function makeTestFrontEndQueryArtist2(): array
    {
        return [
            'query'=>[
                'where'=>[
                    [
                        'type'=>'and',
                        'operator'=>'andX',
                        'conditions'=>[
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['BEEFOVEN']
                            ],
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['Bob Marley']
                            ]
                        ]
                    ],
                    [
                        'type'=>'and',
                        'operator'=>'andX',
                        'conditions'=>[
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['Urethra Franklin']
                            ],
                            [
                                'field'=>'t.name',
                                'operator'=>'neq',
                                'arguments'=>['Khaaan!!']
                            ]
                        ]
                    ],
                    [
                        'field'=>'t.name',
                        'type'=>'and',
                        'operator'=>'neq',
                        'arguments'=>['Gossepi the squid']
                    ],
                ],
                'having'=>[
                    [
                        'field'=>'t.id',
                        'type'=>'or',
                        'operator'=>'neq',
                        'arguments'=>[0]
                    ],
                    [
                        'field'=>'t.id',
                        'type'=>'or',
                        'operator'=>'neq',
                        'arguments'=>[0]
                    ],
                ],
                'placeholders'=>[
                    'frontEndTestPlaceholder'=>[
                        'value'=>777,
                        'type'=>'integer'
                    ],
                    'frontEndTestPlaceholder2'=>[
                        'value'=>'stuff2',
                        'type'=>'string'
                    ]
                ]
            ]
        ];
    }

}
