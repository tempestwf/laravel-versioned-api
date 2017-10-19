<?php

use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Crud\PHPUnit\CrudTestBaseAbstract;


class CrudControllerTest extends CrudTestBaseAbstract
{

    /**
     * @group CrudController
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
     * @group CrudController2
     * @throws Exception
     */
    public function testCreateUpdateDelete () {
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

            $testUser = $userRepo->findOneBy(['id'=>1]);

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => $testUser->getPassword()]);
            $result = $response->decodeResponseJson();

            /** @var string $token */
            $token = $result['token'];
            $this->refreshApplication();

            $create = [
                'token'=>$token,
                'params'=>[
                    [
                        'name'=>'Test Artist'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];
            $response = $this->json('POST', '/admin/artist', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist', $result[0]['name']);

            $update = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$result[0]['id'],
                        'name'=>'Test Artist Updated'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];


            $response = $this->json('PUT', '/admin/artist/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated', $result[0]['name']);

            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again'
                ]
            ];


            $response = $this->json('PUT', '/admin/artist/'. $result[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated Again', $result[0]['name']);

            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again And Again'
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];


            $response = $this->json('PUT', '/admin/artist/'. $result[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertEquals('Test Artist Updated Again And Again', $result[0]['name']);



            $delete = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$result[0]['id'],
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];


            $response = $this->json('DELETE', '/admin/artist/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result[0]['id']);

            $response = $this->json('POST', '/admin/artist', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();



            $delete = [
                'token'=>$token,
                'params'=> [
                ]
            ];


            $response = $this->json('DELETE', '/admin/artist/'. $result[0]['id'], $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result[0]['id']);

            $response = $this->json('POST', '/admin/artist', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $delete = [
                'token'=>$token,
                'params'=> [
                ],
                'options'=>[
                    'simplifiedParams'=>true
                ]
            ];


            $response = $this->json('DELETE', '/admin/artist/'. $result[0]['id'], $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();

            $this->assertNull($result[0]['id']);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }


    /**
     * @group CrudController
     * @throws Exception
     */
    public function testRead () {
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

            $testUser = $userRepo->findOneBy(['id'=>1]);

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => $testUser->getPassword()]);
            $result = $response->decodeResponseJson();

            /** @var string $token */
            $token = $result['token'];
            $this->refreshApplication();


            $testArtist = $artistRepo->findOneBy(['name'=>'Brahms']);
            $response = $this->json('GET', '/artist/' . $testArtist->getId(), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $query = [
                'query'=>[
                    'where'=>[
                        [
                            'field'=>'a.name',
                            'type'=>'and',
                            'operator'=>'eq',
                            'arguments'=>['Brahms']
                        ],
                    ]
                ]
            ];

            $response = $this->json('GET', '/artist?queryLocation=body', array_merge(['token'=>$token], $query), ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $response = $this->json('GET', '/artist?queryLocation=singleParam&query='. json_encode($query), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');


            $response = $this->json('GET', '/artist?queryLocation=params&and_where_eq_a-name=Brahms', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

}
