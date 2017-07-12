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
            $result = $albumRepo->create([
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
            ]);
            $album = $result[0];
            $artist = $album->getArtist();
            $users = $album->getUsers();
            $user = $users[0];
            $this->assertEquals($album->getName(), 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertEquals($artist->getName(), 'BEETHOVEN');
            $this->assertEquals($user->getId(), 1);

            $em->flush();
            $conn->rollBack();
            //$conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
