<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\AlbumRepository;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Helper\ArrayHelper;
use TempestTools\Crud\Constants\EntityEventsConstants;
use TempestTools\Crud\Constants\RepositoryEventsConstants;

class CrudTest extends TestCase
{
    use MakeEmTrait;

    /**
     * @group CrudReadOnly2
     * @throws Exception
     */
    public function testGeneralQueryBuilding () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, ['hydrate'=>true]);
            $this->assertEquals($result['count'], 2);
            $this->assertEquals($result['result'][0]['name'], 'BEETHOVEN');
            $this->assertEquals($result['result'][0]['albums'][0]['name'], 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            /*$qb = $result['qb'];
            $sql = $qb->getQuery()->getSQL();
            $dql = $qb->getQuery()->getDQL();*/
            //$conn->rollBack();

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudReadOnly
     * @throws Exception
     */
    public function testBasicRead () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['user'], ['testing']);
            $result = $userRepo->read();
            $this->assertEquals($result['result'][0]['id'], 1);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    
    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testNullAssignType () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testNullAssignType'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $e = null;
            try {
                /** @var Artist[] $result2 */
                $artistRepo->update([
                    $result[0]->getId() => [
                        'name'=>'The artist formerly known as BEETHOVEN',
                        'albums'=>[
                            'update'=>[
                                $result[0]->getAlbums()[0]->getId() => [
                                    'name'=>'Kick Ass Piano Solos!'
                                ]
                            ]
                        ]
                    ]
                ]);
            } catch (Exception $e){

            }

            $this->assertEquals(get_class($e), \RuntimeException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testUpdateWithChainAndEvents () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            /** @var Artist[] $result2 */
            $result2 = $artistRepo->update([
                $result[0]->getId() => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'name'=>'Kick Ass Piano Solos!'
                            ]
                        ]
                    ]
                ]
            ]);

            $this->assertEquals($result2[0]->getName(), 'The artist formerly known as BEETHOVEN');
            $this->assertEquals($result2[0]->getAlbums()[0]->getName(), 'Kick Ass Piano Solos!');

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray()->getArrayCopy();

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_UPDATE_BATCH,
                 RepositoryEventsConstants::PRE_UPDATE,
                 RepositoryEventsConstants::VALIDATE_UPDATE,
                 RepositoryEventsConstants::VERIFY_UPDATE,
                 RepositoryEventsConstants::PROCESS_RESULTS_UPDATE,
                 RepositoryEventsConstants::POST_UPDATE,
                 RepositoryEventsConstants::POST_UPDATE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testMultiDeleteAndEvents () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            /** @var Artist[] $result2 */
            $result2 = $artistRepo->delete([
                $result[0]->getId() => [

                ],
                $result[1]->getId() => [

                ]
            ]);

            $this->assertNull($result2[0]->getId());
            $this->assertNull($result2[1]->getId());

            /** @noinspection NullPointerExceptionInspection */
            $array = $artistRepo->getArrayHelper()->getArray()->getArrayCopy();

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_DELETE_BATCH,
                 RepositoryEventsConstants::PRE_DELETE,
                 RepositoryEventsConstants::VALIDATE_DELETE,
                 RepositoryEventsConstants::VERIFY_DELETE,
                 RepositoryEventsConstants::PROCESS_RESULTS_DELETE,
                 RepositoryEventsConstants::POST_DELETE,
                 RepositoryEventsConstants::POST_DELETE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }


            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testChainRemove () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->update([
                $result[0]->getId() => [
                    'albums'=>[
                        'update'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'assignType'=>'removeSingle',
                            ],
                            $result[0]->getAlbums()[1]->getId() => [
                                'assignType'=>'removeSingle',
                            ]
                        ]
                    ]
                ]
            ]);

            $this->assertCount(0, $result[0]->getAlbums());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudCudOnly
     * @throws Exception
     */
    public function testChainDelete () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->update([
                $result[0]->getId() => [
                    'albums'=>[
                        'delete'=>[
                            $result[0]->getAlbums()[0]->getId() => [
                                'assignType'=>'removeSingle',
                            ],
                            $result[0]->getAlbums()[1]->getId() => [
                                'assignType'=>'removeSingle',
                            ]
                        ]
                    ]
                ]
            ]);

            $this->assertCount(0, $result[0]->getAlbums());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testNoFlush():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData(), ['flush'=>false]);

            $this->assertEquals($result[0]->getId(), NULL);

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testEventsFire():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            $albumRepo->create($this->createData());

            /** @noinspection NullPointerExceptionInspection */
            $array = $albumRepo->getArrayHelper()->getArray()->getArrayCopy();

            $this->assertArrayHasKey('params', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('arrayHelper', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('results', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('self', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('optionOverrides', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('entitiesShareConfigs', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('frontEndOptions', $array['repoEvents']['preStart']);
            $this->assertArrayHasKey('action', $array['repoEvents']['preStart']);

            foreach ([
                 RepositoryEventsConstants::PRE_START,
                 RepositoryEventsConstants::PRE_STOP,
                 RepositoryEventsConstants::PRE_CREATE_BATCH,
                 RepositoryEventsConstants::PRE_CREATE,
                 RepositoryEventsConstants::VALIDATE_CREATE,
                 RepositoryEventsConstants::VERIFY_CREATE,
                 RepositoryEventsConstants::PROCESS_RESULTS_CREATE,
                 RepositoryEventsConstants::POST_CREATE,
                 RepositoryEventsConstants::POST_CREATE_BATCH
             ] as $event) {
                $this->assertArrayHasKey($event, $array['repoEvents']);
            }

            foreach ([
                 EntityEventsConstants::PRE_SET_FIELD,
                 EntityEventsConstants::PRE_PROCESS_ASSOCIATION_PARAMS,
                 EntityEventsConstants::PRE_PERSIST,
                 EntityEventsConstants::POST_PERSIST,
             ] as $event) {
                $this->assertArrayHasKey($event, $array['entityEvents']);
            }

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testMaxBatch():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);

            $e = NULL;
            try {
                $artistRepo->create([
                    [
                        'name'=>'BEETHOVEN',
                    ],
                    [
                        'name'=>'BACH',
                    ]
                ], ['batchMax'=>1]);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testAssignById():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $albumRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var Artist[] $artists */
            $artists = $artistRepo->create([
                [
                    'name'=>'BEETHOVEN',
                ],
            ]);
            $albums = $albumRepo->create([
                [
                    'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                    'artist'=>$artists[0]->getId(),
                    'releaseDate'=>new \DateTime('now')
                ]
            ]);
            $this->assertEquals($albums[0]->getArtist()->getId(), $artists[0]->getId());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testMultiAddAndChain():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['admin'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);
            /** @var User[] $users */
            $users = $userRepo->create($this->createRobAndBobData());

            $userIds = [];
            /** @var User $user */
            foreach ($users as $user) {
                $userIds[] = $user->getId();
            }

            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds));
            $this->assertEquals($result[0]->getName(), 'BEETHOVEN');
            $this->assertEquals($result[0]->getAlbums()[0]->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertEquals($result[0]->getAlbums()[1]->getName(), 'BEETHOVEN: THE COMPLETE STRING QUARTETS');
            $this->assertEquals($result[0]->getAlbums()[0]->getUsers()[0]->getName(), 'bob');
            $this->assertEquals($result[0]->getAlbums()[0]->getUsers()[1]->getName(), 'rob');
            $this->assertEquals($result[0]->getAlbums()[1]->getUsers()[0]->getName(), 'bob');
            $this->assertEquals($result[0]->getAlbums()[1]->getUsers()[1]->getName(), 'rob');
            $this->assertEquals($result[1]->getName(), 'BACH');
            $this->assertEquals($result[1]->getAlbums()[0]->getName(), 'Amsterdam Baroque Orchestra');
            $this->assertEquals($result[1]->getAlbums()[1]->getName(), 'The English Suites');
            $this->assertEquals($result[1]->getAlbums()[0]->getUsers()[0]->getName(), 'bob');
            $this->assertEquals($result[1]->getAlbums()[0]->getUsers()[1]->getName(), 'rob');
            $this->assertEquals($result[1]->getAlbums()[1]->getUsers()[0]->getName(), 'bob');
            $this->assertEquals($result[1]->getAlbums()[1]->getUsers()[1]->getName(), 'rob');


            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelMutate():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelMutate'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'foobar');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelClosure():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelClosure'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforceOnRelation():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforceOnRelation'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforce():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforce'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testTopLevelClosure():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelClosure'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testEnforceTopLevelWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testEnforceTopLevelWorks'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testTopLevelSetToAndMutate():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'foobar');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testValidatorWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['admin'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $createData = $this->createData();
                $createData[0]['name'] = 'f';
                $albumRepo->create($createData);
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testFastMode1():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testFastMode1'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testFastMode2AndLowLevelSetTo():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testFastMode2'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'foo');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testCreateAlbumAndArtistAndAddUserToAlbum():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['superAdmin'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $artist = $album->getArtist();
            $users = $album->getUsers();
            $user = $users[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertEquals($artist->getName(), 'BEETHOVEN');
            $this->assertEquals($user->getId(), 1);

            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testAllowedWorks():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['default'], ['default']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $albumRepo->create($this->createData());
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks1():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive1'], ['testing']);
            $e = NULL;
            /** @var Album[] $result */
            try {
                $albumRepo->create($this->createData());
            } catch (Exception $e) {

            }
            $this->assertEquals(get_class($e), \RuntimeException::class);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * A basic test example.
     * @group CrudCudOnly
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks2():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive2'], ['testing']);
            /** @var Album[] $result */
            $result = $albumRepo->create($this->createData());
            $album = $result[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $em->flush();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


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
            'where'=>[
                [
                    'type'=>'andX',
                    'conditions'=>[
                        'field'=>'t.name',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN']
                    ]
                ],
                [
                    'type'=>'orX',
                    'conditions'=>[
                        'field'=>'t.name',
                        'operator'=>'eq',
                        'arguments'=>['BEETHOVEN']
                    ]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'and',
                    'operator'=>'eq',
                    'arguments'=>['BEETHOVEN']
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'eq',
                    'arguments'=>['BEETHOVEN']
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'neq',
                    'arguments'=>['Blink 182']
                ],
                [
                    'field'=>'t.id',
                    'type'=>'or',
                    'operator'=>'lt',
                    'arguments'=>[9999999]
                ],
                [
                    'field'=>'t.id',
                    'type'=>'or',
                    'operator'=>'lte',
                    'arguments'=>[9999999]
                ],
                [
                    'field'=>'t.id',
                    'type'=>'or',
                    'operator'=>'gt',
                    'arguments'=>[0]
                ],
                [
                    'field'=>'t.id',
                    'type'=>'or',
                    'operator'=>'gte',
                    'arguments'=>[0]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'in',
                    'arguments'=>[['BEETHOVEN']]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'notIn',
                    'arguments'=>[['Vanilla Ice']]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'isNull',
                    'arguments'=>[]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'isNotNull',
                    'arguments'=>[]
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'like',
                    'arguments'=>['%BEETHOV%']
                ],
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'notLike',
                    'arguments'=>['%The Ruttles%']
                ],
                [
                    'field'=>'t.id',
                    'type'=>'or',
                    'operator'=>'between',
                    'arguments'=>[0,9999999]
                ],
            ],
            'having'=>[
                [
                    'field'=>'t.name',
                    'type'=>'or',
                    'operator'=>'eq',
                    'arguments'=>['BEETHOVEN']
                ],
                [
                    'field'=>'t.name',
                    'type'=>'and',
                    'operator'=>'eq',
                    'arguments'=>['BEETHOVEN']
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
                    'value'=>1,
                    'type'=>'integer'
                ],
                'frontEndTestPlaceholder2'=>[
                    'value'=>'stuff',
                    'type'=>'string'
                ]
            ]
        ];
    }

}
