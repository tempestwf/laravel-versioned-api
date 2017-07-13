<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\AlbumRepository;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use TempestTools\Common\Helper\ArrayHelper;

class CrudCreateTest extends TestCase
{
    use MakeEmTrait;

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
     * A basic test example.
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testLowLevelMutate()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelMutate'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testLowLevelClosure()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelClosure'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforceOnRelation()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforceOnRelation'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testLowLevelEnforce()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testLowLevelEnforce'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testTopLevelClosure()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelClosure'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testEnforceTopLevelWorks()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testEnforceTopLevelWorks'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testTopLevelSetToAndMutate()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testTopLevelSetToAndMutate'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testValidatorWorks()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['admin'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testFastMode1()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testFastMode1'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testFastMode2AndLowLevelSetTo()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testFastMode2'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testCreateAlbumAndArtistAndAddUserToAlbum()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['superAdmin'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testAllowedWorks()
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks1()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive1'], ['default']);
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
     * @group crud
     * @return void
     * @throws Exception
     */
    public function testPermissiveWorks2()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var AlbumRepository $albumRepo */
            $albumRepo = $this->em->getRepository(Album::class);
            $arrayHelper = $this->makeArrayHelper();
            //Test as super admin level permissions to be able to create everything in one fell swoop
            $albumRepo->init($arrayHelper, ['testPermissive2'], ['default']);
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
     * @return array
     */
    protected function createData (): array
    {
        return [
            [
                'name'=>'BEETHOVEN: THE COMPLETE PIANO SONATAS',
                'releaseDate'=>new \DateTime('now'),
                'artist'=>[
                    'create'=>[
                        [
                            'name'=>'BEETHOVEN',
                            'assignType'=>'set'
                        ]
                    ]
                ],
                'users'=>[
                    'read'=>[
                        '1'=>[
                            'assignType'=>'addSingle'
                        ]
                    ]
                ]
            ]
        ];
    }
}
