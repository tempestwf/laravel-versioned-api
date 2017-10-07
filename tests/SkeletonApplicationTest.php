<?php

use TempestTools\Crud\PHPUnit\CrudTestBaseAbstract;


class SkeletonApplicationTest extends CrudTestBaseAbstract
{




    /**
     * @group skeletonApplication
     * @throws Exception
     */
    public function testUserController () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /*$token = $this->getToken();

            $create = [
                'token'=>$token,
                'params'=>[
                    [
                        'name'=>'Test User',
                        'email'=>'test@test.com',
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'albums'=>[
                            'name'=>'Test Album',
                            'artist'=>[
                                'name'=>'The Artist'
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];
            $this->refreshApplication();
            $response = $this->json('POST', '/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $create['params']['roles'] = ['name'=>'test'];

            $response = $this->json('POST', 'admin/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $response = $this->json('POST', 'admin/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            unset($create['params']['roles']);

            $create['params']['permissions'] = ['name'=>'test'];

            $response = $this->json('POST', 'admin/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            unset($create['params']['permissions']);

            $response = $this->json('POST', 'admin/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed


            $create = [
                'token'=>$token,
                'params'=>[
                    [
                        'name'=>'Test User2',
                        'email'=>'test2@test.com',
                        'roles'=>['name'=>'test'],
                        'permissions'=>['name'=>'test'],
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'albums'=>[
                            'name'=>'Test Album',
                            'artist'=>[
                                'name'=>'The Artist'
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];

            $response = $this->json('POST', 'super-admin/user', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $userResult = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $response = $this->json('GET', 'user', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $response = $this->json('GET', 'user/' . $userResult[0]['id'], [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $update = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$userResult[0]['id'],
                        'name'=>'Test User2 Updated',
                        'email'=>'test2@test.com',
                        'roles'=>[
                            'id'=>$userResult[0]['roles'][0]['id'],
                            'name'=>'test'
                        ],
                        'permissions'=>[
                            'id'=>$userResult[0]['permissions'][0]['id'],
                            'name'=>'test'
                        ],
                        'job'=>'doing stuff!',
                        'address'=>'my home!',
                        'albums'=>[
                            'id'=>$userResult[0]['albums'][0]['id'],
                            'name'=>'Test Album',
                            'artist'=>[
                                'id'=>$userResult[0]['albums'][0]['artist'][0]['id'],
                                'name'=>'The Artist'
                            ]
                        ]
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];

            $response = $this->json('PUT', 'user', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $response = $this->json('PUT', 'admin/user', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $response = $this->json('PUT', 'super-admin/user', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed


            $update['params'] = $update['params'][0];
            $response = $this->json('PUT', 'super-admin/user/' . $userResult[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should succeed

            $delete = [
                'token'=>$token,
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
                    'simplifiedParams'=>true
                ]
            ];

            $update['params'] = $update['params'][0];
            $response = $this->json('DELETE', '/user', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->refreshApplication();
            //Assert should fail

            $update['params'] = $update['params'][0];
            $response = $this->json('DELETE', '/admin/user' . $delete, [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
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
