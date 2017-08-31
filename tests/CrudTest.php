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

class CrudTest extends TestCase
{
    use MakeEmTrait;


    /**
     * @group CrudReadOnly5
     * @throws Exception
     */
    public function testReadPermissions () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $arrayHelper = $this->makeArrayHelper();

            $frontEndQuery = $this->makeTestFrontEndQueryArtist();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $optionsOverrides = [];
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testAllowed'], ['testing']);
            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);

            $artistRepo->init($arrayHelper, ['testPermissions1'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.name, operator = eq', $e->getMessage());


            $artistRepo->init($arrayHelper, ['testPermissions2'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.id, operator = lt', $e->getMessage());


            $artistRepo->init($arrayHelper, ['testPermissions3'], ['testing']);

            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderHelperException::class, $e);
            $this->assertEquals('Error: Operator not allowed. field = t.id, operator = gt', $e->getMessage());


            $e = null;
            try {
                $artistRepo->read([
                    'query'=>[
                        'where'=>[
                            [
                                'field'=>'t.name',
                                'type'=>'and',
                                'operator'=>'not a freaking operator',
                                'arguments'=>['no arguments about it']
                            ],
                        ]
                    ]
                ], $frontEndOptions, $optionsOverrides);
            } catch (Exception $e) {

            }

            $this->assertInstanceOf(QueryBuilderWrapperException::class, $e);
            $this->assertEquals('Error: Requested operator is not safe to use. operator = not a freaking operator', $e->getMessage());

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    /**
     * @group CrudReadOnly4
     * @throws Exception
     */
    public function testSqlQueryFunctionality () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testSqlQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist2();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $frontEndOptions['offset'] = 0;
            $optionsOverrides = [
                'hydrate'=>false,
                'paginate'=>false,
                'hydrationType'=>null,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ]
                ],
            ];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['hydrate'] = true;

            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $qb = $result['qb'];

            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $sql = $qb->getSQL();

            $this->assertInstanceOf(\Doctrine\DBAL\Query\QueryBuilder::class, $qb);

            $this->assertEquals($sql,'SELECT * FROM artists t INNER JOIN albums a ON a.artist_id = t.id LEFT JOIN albums a2 ON a.artist_id = t.id WHERE (1 = 1) AND ((:placeholderTest2 = \'some stuff\') AND (:placeholderTest = \'some stuff2\') AND (:frontEndTestPlaceholder = 777) AND (:frontEndTestPlaceholder2 = \'stuff2\') AND (:placeholderTest3 = \'some stuff3\')) AND ((t.name <> :placeholder2ed1f0f3fe3b000e) AND (t.name <> :placeholdere7646f6929cc4da1)) AND ((t.name <> :placeholder13d2d6a6067273d1) AND (t.name <> :placeholderd0c2158016a373e3)) AND (t.name <> :placeholder7689c193d2472a87) HAVING (1 = 1) OR (t.id <> :placeholdercfcd208495d565ef) OR (t.id <> :placeholdercfcd208495d565ef) LIMIT 1 OFFSET 0');
            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff3',
                777,
                'stuff2',
                'some stuff3',
                0,
                0,
                'Gossepi the squid',
                'Khaaan!!',
                'Urethra Franklin',
                'Bob Marley',
                'BEETHOVEN',
            ];

            $existingKeys = [];
            $existingValues = [];
            $simplePlaceholderReference = [];

            foreach ($placeholders as $key => $value) {
                $existingKeys[] = $key;
                $existingValues[] = $value;
                $simplePlaceholderReference[$key] = $value;
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertEquals($result['count'], 16);
            $this->assertEquals($result['result'][0]['name'], 'BEETHOVEN: THE COMPLETE PIANO SONATAS');

            $artistRepo->init($arrayHelper, ['testSqlQueryNoCache'], ['testing']);
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertEquals($result['count'], 16);
            $this->assertEquals($result['result'][0]['name'], 'BEETHOVEN: THE COMPLETE PIANO SONATAS');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudReadOnly3
     * @throws Exception
     */
    public function testGeneralDataRetrieval () {
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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery2'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist2();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $frontEndOptions['offset'] = 0;
            $optionsOverrides = [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ]
                ],
            ];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['hydrate'] = true;

            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];
            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertEquals($dql,'SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE 1 = 1 AND (:placeholderTest2 = \'some stuff\' AND :placeholderTest = \'some stuff2\' AND :frontEndTestPlaceholder = 777 AND :frontEndTestPlaceholder2 = \'stuff2\' AND :placeholderTest3 = \'some stuff3\') AND (t.name <> :placeholder2ed1f0f3fe3b000e AND t.name <> :placeholdere7646f6929cc4da1) AND (t.name <> :placeholder13d2d6a6067273d1 AND t.name <> :placeholderd0c2158016a373e3) AND t.name <> :placeholder7689c193d2472a87 HAVING 1 = 1 OR t.id <> :placeholdercfcd208495d565ef OR t.id <> :placeholdercfcd208495d565ef');


            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff3',
                777,
                'stuff2',
                'some stuff3',
                0,
                0,
                'Gossepi the squid',
                'Khaaan!!',
                'Urethra Franklin',
                'Bob Marley',
                'BEETHOVEN',
            ];

            $existingKeys = [];
            $existingValues = [];
            $simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                $simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

            $foundArtists = [];
            $foundAlbums = [];
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            $foundArtists[] = $result['result'][0]['name'];
            $foundAlbums[] = $result['result'][0]['albums'][0]['name'];
            $this->assertEquals($result['count'], 2);

            $frontEndOptions['offset'] = 1;

            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $foundArtists[] = $result['result'][0]['name'];
            $foundAlbums[] = $result['result'][0]['albums'][0]['name'];

            $this->assertEquals($result['count'], 2);

            $this->assertContains('BEETHOVEN', $foundArtists);
            $this->assertContains('BACH', $foundArtists);

            $this->assertContains('BEETHOVEN: THE COMPLETE PIANO SONATAS', $foundAlbums);
            $this->assertContains('Amsterdam Baroque Orchestra', $foundAlbums);

            $optionsOverrides['hydrationType'] = Query::HYDRATE_OBJECT;
            $optionsOverrides['paginate'] = false;
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertNull($result['count']);

            $this->assertInstanceOf(Artist::class, $result['result'][0]);

            $optionsOverrides['hydrationType'] = Query::HYDRATE_ARRAY;
            $optionsOverrides['paginate'] = true;
            $frontEndOptions['returnCount'] = false;
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertNull($result['count']);

            $frontEndOptions['returnCount'] = true;
            $frontEndOptions['offset'] = 0;
            $frontEndOptions['limit'] = 2;
            $result1 = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $optionsOverrides['fetchJoin'] = false;

            $result2 = $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);

            $this->assertCount(2, $result1['result']);

            $this->assertCount(1, $result2['result']);

            $optionsOverrides['maxLimit'] = 1;
            $e = null;
            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (\Exception $e) {

            }

            $this->assertInstanceOf( QueryBuilderHelperException::class, $e);


            $optionsOverrides['maxLimit'] = 100;

            $optionsOverrides['queryMaxParams'] = 1;

            try {
                $artistRepo->read($frontEndQuery, $frontEndOptions, $optionsOverrides);
            } catch (\Exception $e) {

            }

            $this->assertInstanceOf( QueryBuilderHelperException::class, $e);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }





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

            /** @var Artist[] $result */
            $artistRepo->create($this->createArtistChainData($userIds));
            $artistRepo->init($arrayHelper, ['testQuery'], ['testing']);

            $frontEndQuery = $this->makeTestFrontEndQueryArtist();
            $frontEndOptions = $this->makeFrontEndQueryOptions();
            $result = $artistRepo->read($frontEndQuery, $frontEndOptions, [
                'hydrate'=>false,
                'placeholders'=>[
                    'placeholderTest3'=>[
                        'value'=>'some stuff3',
                    ]
                ],
            ]);
            /** @var  \Doctrine\ORM\QueryBuilder $qb */
            /** @var \Doctrine\ORM\Query $query */
            $query = $result['query'];
            $qb = $result['qb'];
            $paginator = $result['paginator'];
            $qbWrapper = $result['qbWrapper'];
            /** @var Doctrine\ORM\Query\Parameter[] $placeholders */
            $placeholders = $qb->getParameters();
            $dql = $query->getDQL();

            $this->assertInstanceOf(\Doctrine\ORM\Query::class, $query);
            $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);
            $this->assertInstanceOf(\Doctrine\ORM\Tools\Pagination\Paginator::class, $paginator);
            $this->assertInstanceOf(\TempestTools\Crud\Doctrine\Wrapper\QueryBuilderDqlWrapper::class, $qbWrapper);
            $lifeTime = $query->getResultCacheLifetime();

            $cacheId = $query->getResultCacheId();

            $this->assertEquals($lifeTime, 777);
            $this->assertEquals($cacheId, 'test_cache_id');
            $queryCacheDrive = $query->getQueryCacheDriver();
            $resultCacheDrive = $query->getResultCacheDriver();

            /** @noinspection NullPointerExceptionInspection */
            $arrayCache = $artistRepo->getArrayHelper()->getArray()['arrayCache'];

            $this->assertSame($arrayCache, $queryCacheDrive);
            $this->assertSame($arrayCache, $resultCacheDrive);


            $this->assertEquals($dql,'SELECT t, a FROM App\API\V1\Entities\Artist t INNER JOIN t.albums a WITH 1 = 1 LEFT JOIN t.albums a2 WITH 1 = 1 WHERE ((((1 = 1 OR 0 <> 1 OR 0 < 1 OR 0 <= 1 OR 1 > 0 OR 1 >= 0 OR t.id IN(1, 0) OR t.id NOT IN(1, 0) OR t.id IS NULL OR t.id IS NOT NULL OR t.name LIKE \'%BEE%\' OR t.name NOT LIKE \'%VAN%\' OR (t.id BETWEEN 0 AND 2)) AND 1 = 1) OR 1 = 1) AND (t.name = :placeholderad553ad84c1ba11a AND t.name <> :placeholdere7646f6929cc4da1) AND t.name = :placeholder5585b8340ac2182b AND t.name = :placeholder250cc8f7b77a15af AND t.name <> :placeholder50ae8bca45384643 AND t.id < :placeholderf30f7d1907f12e32 AND t.id <= :placeholdere9e3789bfb59e910 AND t.id > :placeholder6bb61e3b7bce0931 AND t.id >= :placeholder5d7b9adcbe1c629e AND t.name IN(:placeholder3b9b9e6a2b055833) AND t.name NOT IN(:placeholder1cf3b2433d6e6986) AND t.name IS NULL AND t.name IS NOT NULL AND t.name LIKE :placeholder52bb4eb0974ded8c AND t.name NOT LIKE :placeholderfa7b4ec623968f9a AND (t.id BETWEEN :placeholdercfcd208495d565ef AND :placeholder37ebc6efcc49ae93)) OR (t.name = :placeholder9124f75f1451ed7e OR t.name <> :placeholder13d2d6a6067273d1) GROUP BY t.name, t.name, t.id HAVING (((1 = 1 AND 1 = 1) OR 1 = 1) AND t.name = :placeholder5cde382208614d76) OR t.name = :placeholderf6b05f37a61192d6 ORDER BY t.id DESC, t.name ASC, t.id DESC');
            $placeholderKeysToTest = ['placeholderTest2', 'placeholderTest', 'frontEndTestPlaceholder', 'frontEndTestPlaceholder2', 'placeholderTest3'];
            $placeholderValuesToTest = [
                'some stuff',
                'some stuff2',
                '777',
                'stuff2',
                'some stuff3',
                'BEETHOVEN1',
                'BEETHOVEN2',
                'BEETHOVEN3',
                'BEETHOVEN4',
                'Blink 182',
                '99999991',
                '99999992',
                '-1',
                '-2',
                'BEETHOVEN5',
                ['Vanilla Ice'],
                '%BEETHOV%',
                '%The Ruttles%',
                '0',
                '99999993',
                'BEETHOVEN6',
                'BEETHOVEN7',
                'Bob Marley',
                'Urethra Franklin'
            ];

            $existingKeys = [];
            $existingValues = [];
            //$simplePlaceholderReference = [];

            foreach ($placeholders as $placeholder) {
                $existingKeys[] = $placeholder->getName();
                $existingValues[] = $placeholder->getValue();
                //$simplePlaceholderReference[$placeholder->getName()] = $placeholder->getValue();
            }

            foreach ($placeholderKeysToTest as $key) {
                $this->assertContains($key, $existingKeys);
            }

            foreach ($placeholderValuesToTest as $value) {
                $this->assertContains($value, $existingValues);
            }

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

            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
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
            $this->assertEquals(get_class($e), DataBindHelperException::class);

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
            /** @var Album[] $albums */
            /** @var Artist $artist */
            $artist = $albums[0]->getArtist();
            $this->assertEquals($artist->getId(), $artists[0]->getId());

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
            $user = $result[0]->getAlbums()[0]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[0]->getAlbums()[0]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $user = $result[0]->getAlbums()[1]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[0]->getAlbums()[1]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $this->assertEquals($result[1]->getName(), 'BACH');
            $this->assertEquals($result[1]->getAlbums()[0]->getName(), 'Amsterdam Baroque Orchestra');
            $this->assertEquals($result[1]->getAlbums()[1]->getName(), 'The English Suites');
            $user = $result[1]->getAlbums()[0]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[1]->getAlbums()[0]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');
            $user = $result[1]->getAlbums()[1]->getUsers()[0];
            $this->assertEquals($user->getName(), 'bob');
            $user = $result[1]->getAlbums()[1]->getUsers()[1];
            $this->assertEquals($user->getName(), 'rob');

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);

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
            $this->assertEquals(get_class($e), EntityException::class);

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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
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
            $this->assertEquals(get_class($e), EntityArrayHelperException::class);
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
