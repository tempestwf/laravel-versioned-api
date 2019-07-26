<?php

use App\API\V1\Entities\Album;
use App\API\V1\Entities\Artist;
use App\API\V1\Entities\Permission;
use App\API\V1\Entities\Role;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\UnitTest\CrudTestBase;
use Faker\Factory;


class SkeletonApplicationTest extends CrudTestBase
{

    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testPermissionsController ()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        [$user, $album, $artist, $role, $permission] = $this->getFixtureData();
        $time = new DateTime();
        try {
            $token = $this->getToken();
            $response = $this->json('GET', '/contexts/super-admin/permissions', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/super-admin/permissions/' . $permission->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $create = [
                'params'=>[
                    'name'=>'Test Permission',
                    'users'=>[
                        [
                            'id'=>$user->getId()
                        ]
                    ],
                    'roles'=>[
                        [
                            'name'=>'Test Role'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/super-admin/permissions', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    'id'=>$permission->getId(),
                    'name'=>'Test Permission',
                    'users'=>[
                        [
                            'id'=>$user->getId(),
                            'firstName'=>'Test User',
                            'assignType'=>'null'
                        ]
                    ],
                    'roles'=>[
                        [
                            'id'=>$permission->getId(),
                            'name'=>'Test Role',
                            'assignType'=>'null'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('PUT', '/contexts/super-admin/permissions/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/super-admin/permissions/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testRolesController ()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        [$user, $album, $artist, $role, $permission] = $this->getFixtureData();
        $time = new DateTime();
        try {
            $token = $this->getToken();
            $response = $this->json('GET', '/contexts/super-admin/roles', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/super-admin/roles/' . $role->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $create = [
                'params'=>[
                    'name'=>'Test Role',
                    'users'=>[
                        [
                            'id'=>$user->getId()
                        ]
                    ],
                    'permissions'=>[
                        [
                            'name'=>'Test Permission'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/super-admin/roles', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    'id'=>$role->getId(),
                    'name'=>'Test Role2',
                    'users'=>[
                        [
                            'id'=>$user->getId(),
                            'firstName'=>'Test User',
                            'assignType'=>'null'
                        ]
                    ],
                    'permissions'=>[
                        [
                            'id'=>$permission->getId(),
                            'name'=>'Test Permission',
                            'assignType'=>'null'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('PUT', '/contexts/super-admin/roles/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/super-admin/roles/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testArtistController ()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        [$user, $album, $artist, $role, $permission] = $this->getFixtureData();
        $time = new DateTime();
        try {
            $token = $this->getToken();
            $response = $this->json('GET', '/contexts/guest/artists', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/guest/artists/' . $artist->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/artists', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/artists/' . $artist->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $create = [
                'params'=>[
                    'name'=>'Test Artist',
                    'albums'=>[
                        [
                            'name'=>'Test Album',
                            'releaseDate'=>$time->format('Y-m-d H:i:s'),
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/user/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can't make artists should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('POST', '/contexts/admin/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Admins can make artists should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    [
                        'id'=>$artist->getId(),
                        'name'=>'Test Artist updated',
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('PUT', '/contexts/user/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can't update artists, should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('PUT', '/contexts/admin/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Admins can update artists, should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/user/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can't delete artists, should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/admin/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Admins can delete artists, should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testAlbumController () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        [$user, $album, $artist, $role, $permission] = $this->getFixtureData();
        $time = new DateTime();
        try {
            $token = $this->getToken();
            $response = $this->json('GET', '/contexts/guest/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);


            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/guest/albums/' . $album->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);


            $this->refreshApplication();
            $token = $this->getToken();
            $response = $this->json('GET', '/contexts/user/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/albums/' . $album->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/users/' . $user->getId() . '/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/users/' . -1 . '/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/admin/users/' . -1 . '/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should return empty
            $this->assertEquals([], $result['result']);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/guest/artists/' . $artist->getId() . '/albums', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);

            $this->refreshApplication();
            $create = [
                'params'=>[
                    [
                        'name'=>'Test Album',
                        'releaseDate'=>$time->format('Y-m-d H:i:s'),
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('POST', '/contexts/user/albums', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            // Users can't make albums so fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('POST', '/contexts/admin/albums', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            // Admins can so succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    [
                        'id'=>$album->getId(),
                        'users'=>[
                            'id'=>$user->getId(),
                            'assignType'=>'removeSingle'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('PUT', '/contexts/user/albums/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            // Users can remove them selves from an album so succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    [
                        'id'=>$album->getId(),
                        'users'=>[
                            'id'=>-1,
                            'assignType'=>'removeSingle'
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('PUT', '/contexts/user/albums/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can't remove other people from their albums so fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $delete = [
                'params'=>[
                    [
                        'id'=>$album->getId(),
                        'name'=>'Test Album',
                        'releaseDate'=>$time->format('Y-m-d H:i:s'),
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response = $this->json('DELETE', '/contexts/user/albums/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can't delete albums so fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/admin/albums/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Users can delete albums so succeed
            $this->assertArrayHasKey('id', $result[0]);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
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
            $this->refreshApplication();
            $generator = Factory::create();
            $create = [
                'params'=>[
                    [
                        'email' => $generator->safeEmail,
                        'firstName'=> $generator->firstName,
                        'middleInitial'=>'X',
                        'lastName'=> $generator->lastName,
                        'age' => $generator->randomNumber(2),
                        'gender' => 1,
                        'weight' => 210,
                        'height' => 180.34,
                        'phoneNumber' => "+1 757-571-2711",
                        'lifestyle' => 1,
                        'password' => $generator->password,
                        'job' => $generator->jobTitle,
                        'address' => $generator->address,
                        'locale' => "en",
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
            $response = $this->json('POST', '/contexts/user/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail, user level can't make other users
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $create['params'][0]['roles'] = [['name'=>'test']];
            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail, admins can't do anything with roles
            $this->assertEquals( 500, $result['status_code']);

            unset($create['params'][0]['roles']);

            $this->refreshApplication();
            $create['params'][0]['permissions'] = [['name'=>'test']];
            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail, user level can't do anything with permissions
            $this->assertEquals( 500, $result['status_code']);

            unset($create['params'][0]['permissions']);

            $this->refreshApplication();
            $response = $this->json('POST', '/contexts/admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail, admin level can't do anything with permissions
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $create = [
                'params'=>[
                    [
                        'email' => "test2@test.com",
                        'firstName'=> 'Test',
                        'middleInitial'=>'b',
                        'lastName'=>'User2',
                        'age' => 32,
                        'gender' => 1,
                        'weight' => 210,
                        'height' => 180.34,
                        'phoneNumber' => "+1 757-571-2711",
                        'lifestyle' => 1,
                        'password' => "Zipityzapity00!",
                        'locale' => "en",
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
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
            $response = $this->json('POST', '/contexts/super-admin/users', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/users', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result['result'][0]);
            //$allUsers = $this->em->getRepository(\App\API\V1\Entities\User::class)->findAll();

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/user/users/' . $user->getId(), [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertEquals($user->getId(), $result['id']);

            $this->refreshApplication();
            $update = [
                'params'=>[
                    [
                        'id'=>$user->getId(),
                        'firstName'=>'Test Updated',
                        'middleInitial'=>'b',
                        'lastName'=>'User2',
                        'age' => 32,
                        'gender' => 1,
                        'weight' => 210,
                        'height' => 180.34,
                        'phoneNumber' => "+1 757-571-2711",
                        'lifestyle' => 1,
                        'password' => "Zipityzapity00!",
                        'locale' => "en",
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
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
            //Assert should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('PUT', '/contexts/admin/users/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('PUT', '/contexts/super-admin/users/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $update['params'] = $update['params'][0];
            $response = $this->json('PUT', '/contexts/super-admin/users/' . $user->getId(), $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed
            $this->assertArrayHasKey('id', $result);

            $this->refreshApplication();
            $delete = [
                'params'=>[
                    [
                        'id'=>$user->getId(),
                        'email' => 'test2@test.com',
                        'firstName'=>'Test Updated',
                        'middleInitial'=>'b',
                        'lastName'=>'User2',
                        'age' => 32,
                        'gender' => 1,
                        'weight' => 210,
                        'height' => 180.34,
                        'phoneNumber' => "+1 757-571-2711",
                        'lifestyle' => 1,
                        'password' => "Zipityzapity00!",
                        'locale' => "en",
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
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
            //Assert should fail
            $this->assertEquals( 500, $result['status_code']);

            $this->refreshApplication();
            $response = $this->json('DELETE', '/contexts/super-admin/users/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed*/
            $this->assertArrayHasKey('id', $result[0]);

            $this->refreshApplication();
            $delete['params'] = $delete['params'][0];
            $response = $this->json('DELETE', '/contexts/super-admin/users/' . $user->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            //Assert should succeed*/
            $this->assertArrayHasKey('id', $result);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * @group SkeletonApplication
     * @throws Exception
     */
    public function testContexts () {
        $em = $this->em();
        /** @var UserRepository $userRepo */
        $userRepo = $em->getRepository(User::class);
        $testUser = $userRepo->findOneBy(['email'=>env('BASE_USER_EMAIL')]);

        $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => env('BASE_USER_PASSWORD')]);
        $result = $response->decodeResponseJson();

        $this->refreshApplication();
        /** @var string $token */
        $token = $result['token'];
        $response = $this->json('GET', '/contexts', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('guest', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('admin', $result);
        $this->assertArrayHasKey('super-admin', $result);

        $this->refreshApplication();
        $response = $this->json('GET', '/contexts/guest', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('description', $result);

        $this->refreshApplication();
        $response = $this->json('GET', '/contexts/user', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('description', $result);

        $this->refreshApplication();
        $response = $this->json('GET', '/contexts/admin', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('description', $result);

        $this->refreshApplication();
        $response = $this->json('GET', '/contexts/super-admin', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
        $result = $response->decodeResponseJson();
        $this->assertArrayHasKey('description', $result);
    }

    /**
     * @return array
     */
    protected function getFixtureData():array
    {
        $em = $this->em();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>env('BASE_USER_EMAIL')]);
        $album = $em->getRepository(Album::class)->findOneBy(['name'=>'Brahms: Complete Edition']);
        $artist = $em->getRepository(Artist::class)->findOneBy(['name'=>'Brahms']);
        $role = $em->getRepository(Role::class)->findOneBy(['name'=>'user']);
        $permission = $em->getRepository(Permission::class)->findOneBy(['name'=>'auth/me:GET']);
        return [$user, $album, $artist, $role, $permission];

    }
}
