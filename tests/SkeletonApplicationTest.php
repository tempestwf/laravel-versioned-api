<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Crud\PHPUnit\CrudTestBaseAbstract;


class SkeletonApplicationTest extends CrudTestBaseAbstract
{


    /**
     * @return array
     */
    protected function getFixtureData():array
    {
        $em = $this->em();
        $user = $em->getRepository(User::class)->findOneBy(['id'=>1]);
        $album = $em->getRepository(Album::class)->findOneBy(['name'=>'Brahms: Complete Edition']);
        $artist = $em->getRepository(Artist::class)->findOneBy(['name'=>'Brahms']);
        return [$user, $album, $artist];

    }
    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testContexts () {
        $em = $this->em();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository(User::class);
        $testUser = $userRepo->findOneBy(['id'=>1]);

        $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => $testUser->getPassword()]);
        $result = $response->decodeResponseJson();

        /** @var string $token */
        $token = $result['token'];
        $this->refreshApplication();

        $response = $this->json('GET', '/contexts', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();

        $this->assertArrayHasKey('guest', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('admin', $result);
        $this->assertArrayHasKey('super-admin', $result);

        $response = $this->json('GET', '/contexts/guest', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();

        $this->assertArrayHasKey('description', $result);

        $response = $this->json('GET', '/contexts/user', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();

        $this->assertArrayHasKey('description', $result);

        $response = $this->json('GET', '/contexts/admin', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();

        $this->assertArrayHasKey('description', $result);

        $response = $this->json('GET', '/contexts/super-admin', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();

        $this->assertArrayHasKey('description', $result);
    }

    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testUserController () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        [$user, $album, $artist] = $this->getFixtureData();
        try {
            $token = $this->getToken();
            $time = new DateTime();
            $create = [
                'params'=>[
                    [
                        'name'=>'Test User',
                        'email'=>'test@test.com',
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'password'=>'zipityzapity',
                        'albums'=>[
                            [
                                'name'=>'Test Album',
                                'releaseDate'=>$time->format('Y-m-d H:i:s'),
                                'artist'=>[
                                    'name'=>'The Artist'
                                ]
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $this->refreshApplication();
            $response = $this->json('POST', '/contexts/user/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail, user level can't make other users

            $this->assertEquals( 500, $result['status_code']);

            $create['params'][0]['roles'] = [['name'=>'test']];

            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail, admins can't do anything with roles

            $this->assertEquals( 500, $result['status_code']);


            unset($create['params'][0]['roles']);

            $create['params'][0]['permissions'] = [['name'=>'test']];

            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail, user level can't do anything with permissions

            $this->assertEquals( 500, $result['status_code']);

            unset($create['params'][0]['permissions']);

            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed, with out permissions and roles it should work

            $this->assertArrayHasKey('id', $result[0]);

            $create = [
                'params'=>[
                    [
                        'name'=>'Test User2',
                        'email'=>'test2@test.com',
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'password'=>'zipityzapity',
                        'albums'=>[
                            [
                                'name'=>'Test Album2',
                                'releaseDate'=>$time->format('Y-m-d H:i:s'),
                                'artist'=>[
                                    'name'=>'The Artist2'
                                ]
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];

            $this->refreshApplication();
            $response = $this->json('POST', '/contexts/super-admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $userResult = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertArrayHasKey('id', $result[0]);

            $response = $this->json('GET', '/contexts/user/users', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            //$allUsers = $this->em->getRepository(\App\API\V1\Entities\User::class)->findAll();


            $response = $this->json('GET', '/contexts/user/users/' . $user->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $update = [
                'params'=>[
                    [
                        'id'=>$user->getId(),
                        'name'=>'Test User2 Updated',
                        'email'=>'test2@test.com',
                        'roles'=>[
                            //'id'=>$userResult[0]['roles'][0]['id'],
                            'name'=>'test'
                        ],
                        'permissions'=>[
                            //'id'=>$userResult[0]['permissions'][0]['id'],
                            'name'=>'test'
                        ],
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'albums'=>[
                            'id'=>$album->getId(),
                            'name'=>'Test Album',
                            'artist'=>[
                                'id'=>$artist->getId(),
                                'name'=>'The Artist'
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];

            $response = $this->json('PUT', '/contexts/user/users', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $response = $this->json('PUT', '/contexts/admin/users', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $response = $this->json('PUT', '/contexts/super-admin/users', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed


            $update['params'] = $update['params'][0];
            $response = $this->json('PUT', '/contexts/super-admin/users/' . $userResult[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $delete = [
                'params'=>[
                    [
                        'id'=>$userResult[0]['id'],
                        'name'=>'Test User2 Updated',
                        'email'=>'test2@test.com',
                        'roles'=>[
                            'id'=>$userResult[0]['roles'][0]['id'],
                        ],
                        'permissions'=>[
                            'id'=>$userResult[0]['permissions'][0]['id'],
                        ],
                        'albums'=>[
                            'id'=>$userResult[0]['albums'][0]['id'],
                            'artist'=>[
                                'id'=>$userResult[0]['albums'][0]['artist'][0]['id'],
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];

            $update['params'] = $update['params'][0];
            $response = $this->json('DELETE', '/contexts/user/users', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $update['params'] = $update['params'][0];
            $response = $this->json('DELETE', '/contexts//admin/users' . $delete, [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed*/

            $this->refreshApplication();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }




}
