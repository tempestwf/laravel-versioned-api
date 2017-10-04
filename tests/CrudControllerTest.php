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
    public function testIndex () {
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
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

}
