<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\Permission;
use App\API\V1\Entities\Role;
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
        $role = $em->getRepository(Role::class)->findOneBy(['name'=>'user']);
        $permission = $em->getRepository(Permission::class)->findOneBy(['name'=>'auth/me:GET']);
        return [$user, $album, $artist, $role, $permission];

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
        [$user, $album, $artist, $role, $permission] = $this->getFixtureData();
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
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertArrayHasKey('id', $result[0]);

            $response = $this->json('GET', '/contexts/user/users', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertArrayHasKey('id', $result['result'][0]);
            //$allUsers = $this->em->getRepository(\App\API\V1\Entities\User::class)->findAll();


            $response = $this->json('GET', '/contexts/user/users/' . $user->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertEquals($user->getId(), $result['result'][0]['id']);
            $update = [
                'params'=>[
                    [
                        'id'=>$user->getId(),
                        'name'=>'Test User2 Updated',
                        'email'=>'test2@test.com',
                        'roles'=>[
                            [
                                'name'=>'test'
                            ]
                        ],
                        'permissions'=>[
                            [
                                'name'=>'test'
                            ]
                        ],
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'albums'=>[
                            [
                                'id'=>$album->getId(),
                                'assignType'=>'null',
                                'name'=>'Test Album',
                                'artist'=>[
                                    'id'=>$artist->getId(),
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

            $response = $this->json('PUT', '/contexts/user/users/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $response = $this->json('PUT', '/contexts/admin/users/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $response = $this->json('PUT', '/contexts/super-admin/users/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertArrayHasKey('id', $result[0]);

            $update['params'] = $update['params'][0];
            $response = $this->json('PUT', '/contexts/super-admin/users/' . $user->getId(), $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $this->assertArrayHasKey('id', $result[0]);

            $delete = [
                'params'=>[
                    [
                        'id'=>$user->getId(),
                        'name'=>'Test User2 Updated',
                        'email'=>'test2@test.com',
                        'roles'=>[
                            [
                                'id'=>$role->getId(),
                            ]
                        ],
                        'permissions'=>[
                            [
                                'id'=>$permission->getId(),
                            ]
                        ],
                        'albums'=>[
                            [
                                'id'=>$album->getId(),
                                'artist'=>[
                                    'id'=>$artist->getId(),
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

            $response = $this->json('DELETE', '/contexts/user/users/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $this->assertEquals( 500, $result['status_code']);

            $response = $this->json('DELETE', '/contexts/super-admin/users/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed*/

            $this->assertArrayHasKey('id', $result[0]);

            $delete['params'] = $delete['params'][0];
            $response = $this->json('DELETE', '/contexts/super-admin/users/' . $user->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed*/

            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }




}
