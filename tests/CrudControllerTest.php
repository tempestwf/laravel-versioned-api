<?php

use App\API\V1\Entities\Artist;
use App\API\V1\Entities\User;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\Repositories\UserRepository;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;


class CrudControllerTest extends CrudTestBaseAbstract
{

    /**
     * @return Artist
     */
    protected function makeTestArtist():Artist
    {
        $artist = new Artist();
        $artist->setName('Test Artist');
        $this->em()->persist($artist);
        $this->em()->flush();
        return $artist;
    }

    /**
     * @return Artist
     */
    protected function getDefaultArtist():Artist
    {
        $artistRepo = $this->em->getRepository(Artist::class);
        $artist = $artistRepo->findOneBy(['name'=>'Brahms']);
        return $artist;
    }
    /**
     * @group CrudControllerWhatsup
     * @throws Exception
     */
    public function testCreateUpdateDelete () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        $artistRepo = null;
        try {
            $arrayHelper = $this->makeArrayHelper();
            /** @var ArtistRepository $artistRepo */
            $artistRepo = $this->em->getRepository(Artist::class);
            $artistRepo->init($arrayHelper, ['testNullAssignType'], ['testing']);
            /** @var UserRepository $userRepo */
            $userRepo = $this->em->getRepository(User::class);
            $userRepo->init($arrayHelper, ['testing'], ['testing']);

            $testUser = $userRepo->findOneBy(['id'=>1]);

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => 'password']);
            $result = $response->decodeResponseJson();

            $this->refreshApplication();
            /** @var string $token */
            $token = $result['token'];
            $create = [
                'token'=>$token,
                'params'=>[
                    'name'=>'Test Artist'
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response1 = $this->json('POST', '/contexts/admin/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result1 = $response1->decodeResponseJson();
            $this->assertEquals('Test Artist', $result1['name']);

            $this->refreshApplication();
            $create = [
                'token'=>$token,
                'params'=>[
                    [
                        'name'=>'Test Artist'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response2 = $this->json('POST', '/contexts/admin/artists', $create, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result2 = $response2->decodeResponseJson();
            $this->assertEquals('Test Artist', $result2[0]['name']);

            $this->refreshApplication();
            $artist = $this->getDefaultArtist();
            $update = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=> $artist->getId(),
                        'name'=>'Test Artist Updated'
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response3 = $this->json('PUT', '/contexts/admin/artists/batch', $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result3 = $response3->decodeResponseJson();
            $this->assertEquals('Test Artist Updated', $result3[0]['name']);

            $this->refreshApplication();
            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again'
                ],
                'options'=>[
                    'testMode'=>true
                ]
            ];
            $response4 = $this->json('PUT', '/contexts/admin/artists/'. $result3[0]['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result4 = $response4->decodeResponseJson();
            $this->assertEquals('Test Artist Updated Again', $result4['name']);

            $this->refreshApplication();
            $update = [
                'token'=>$token,
                'params'=> [
                    'name'=>'Test Artist Updated Again And Again'
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response5 = $this->json('PUT', '/contexts/admin/artists/'. $result4['id'], $update, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result5 = $response5->decodeResponseJson();
            $this->assertEquals('Test Artist Updated Again And Again', $result5['name']);

            $this->refreshApplication();
            $delete = [
                'token'=>$token,
                'params'=>[
                    [
                        'id'=>$result5['id'],
                    ]
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response6 = $this->json('DELETE', '/contexts/admin/artists/batch', $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result6 = $response6->decodeResponseJson();
            /* SoftDeleteable still sends in the id */
            $this->assertEquals($result6[0]['id'], $result5['id']);

            $this->refreshApplication();
            $delete = [
                'token'=>$token,
                'params'=> [
                ],
                'options'=>[
                    'testMode'=>true
                ]
            ];
            $response7 = $this->json('DELETE', '/contexts/admin/artists/'. $artist->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result7 = $response7->decodeResponseJson();
            $this->assertEquals($result7['id'], $artist->getId());

            $this->refreshApplication();
            $delete = [
                'token'=>$token,
                'params'=> [
                ],
                'options'=>[
                    'simplifiedParams'=>true,
                    'testMode'=>true
                ]
            ];
            $response8 = $this->json('DELETE', '/contexts/admin/artists/'. $artist->getId(), $delete, ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result8 = $response8->decodeResponseJson();
            $this->assertEquals($result8['id'], $artist->getId());

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

            $response = $this->json('POST', '/auth/authenticate', ['email' => $testUser->getEmail(), 'password' => 'password']);
            $result = $response->decodeResponseJson();

            $this->refreshApplication();
            /** @var string $token */
            $token = $result['token'];
            $testArtist = $artistRepo->findOneBy(['name'=>'Brahms']);
            $response = $this->json('GET', '/contexts/guest/artists/' . $testArtist->getId(), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['name'], 'Brahms');

            $this->refreshApplication();
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
            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=body', array_merge(['token'=>$token], $query), ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=singleParam&query='. json_encode($query), ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $this->refreshApplication();
            $response = $this->json('GET', '/contexts/guest/artists?queryLocation=params&and_where_eq_a-name=Brahms', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['result'][0]['name'], 'Brahms');

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

}
