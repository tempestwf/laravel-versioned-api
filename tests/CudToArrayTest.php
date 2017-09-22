<?php

use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Common\Doctrine\Transformers\ToArrayTransformer;
use TempestTools\Crud\PHPUnit\CrudTestBaseAbstract;


class CudToArrayTest extends CrudTestBaseAbstract
{


    /**
     * @group CudToArray
     * @throws Exception
     */
    public function testToArrayBasicFunctionality () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();


        $uow = $this->em()->getUnitOfWork();
        $uow->computeChangeSets();

        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testTurnOffPrePopulate'], ['testing']);
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

            $optionsOverride = ['clearPrePopulatedEntitiesOnFlush'=>false];
            //Test as super admin level permissions to be able to create everything in one fell swoop
            /** @var Artist[] $result */
            $result = $artistRepo->create($this->createArtistChainData($userIds), $optionsOverride);

            $transformer = new ToArrayTransformer([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);
            $transformed = $transformer->transform($result);

            $this->assertEquals($transformed[0]['name'], 'BEETHOVEN');
            $this->assertEquals($transformed[0]['albums'][0]['name'], 'BEETHOVEN: THE COMPLETE PIANO SONATAS');
            $this->assertCount(2, $transformed[0]['albums']);
            $this->assertTrue(is_string($transformed[0]['albums'][0]['releaseDate']['timezoneName']));
            $this->assertTrue(is_string($transformed[0]['albums'][0]['releaseDate']['formatted']));
            $this->assertTrue(is_int($transformed[0]['albums'][0]['releaseDate']['timestamp']));
            $this->assertTrue(is_int($transformed[0]['albums'][0]['releaseDate']['offset']));
            $this->assertEquals($transformed[0]['albums'][0]['artist']['name'], 'BEETHOVEN');

            $this->assertEquals($transformed[1]['name'], 'BACH');
            $this->assertCount(2, $transformed[1]['albums']);

            $transformer = new ToArrayTransformer([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'limited',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);
            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('users', $transformed[0]['albums'][1]);

            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'minimal',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertEmpty( $transformed[0]['albums'][1]);

            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'none',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);
            $this->assertEmpty($transformed[0]);

            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'maxDepth'=>2,
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertEmpty($transformed[0]['albums'][0]);

            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'excludeKeys'=>['users'],
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('users', $transformed[0]['albums'][0]);


            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayNotHasKey('email', $transformed[0]['albums'][0]['users'][0]);

            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'forceIncludeKeys'=>['id', 'email']
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            $this->assertArrayHasKey('email', $transformed[0]['albums'][0]['users'][0]);


            $id = $result[0]->getId();
            $em->clear();

            $result = $artistRepo->update([
                $id => [
                    'name'=>'The artist formerly known as BEETHOVEN',
                ]
            ]);


            $transformer->setSettings([
                'frontEndOptions'=>[
                    'toArray'=>[
                        'completeness'=>'full',
                        'allowOnlyRequestedParams'=>false
                    ]
                ]
            ]);

            $transformed = $transformer->transform($result);

            //Make sure eager load not triggered
            $this->assertEmpty($transformed[0]['albums']);



            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }




}
