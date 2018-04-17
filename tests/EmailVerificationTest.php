<?php

use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\UserRepository;
use Faker\Factory;
use App\API\V1\Entities\User;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;

class EmailVerificationTest extends CrudTestBaseAbstract
{
    protected $password = '441520435a0a2dac143af05b55f4b751';


    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group aclMiddleware
     * @return void
     * @throws Exception
     */
    public function testGuestAccess():void
    {
        $generator = Factory::create();
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $email = $generator->safeEmail;
            $password = $this->password;
            /** Register user via the guest endpoint **/
            $response = $this->json(
                'POST', '/contexts/guest/users',
                [
                    "params" => [
                        "name" => $generator->name,
                        "email" => $email,
                        "password" => $password,
                        "job" => $generator->jobTitle,
                        "address" => $generator->address,
                        "locale" => "en"
                    ],
                    "options" => [
                        "email" => false,
                        "g-recaptcha-response-omit" => env('GOOGLE_RECAPTCHA_SKIP_CODE') /** skipping recaptcha with a key **/
                    ]
                ]
            );
            $userResult = $response->decodeResponseJson();
            $this->assertArrayHasKey('id', $userResult);

            /** Test guest access email verification index **/
            $response = $this->json('GET', "/contexts/guest/email-verification",[]);
            $response->assertResponseStatus(404);

            /** Test guest access with wrong id **/
            $response = $this->json('GET', "/contexts/guest/email-verification/wrongIdHere!!!",[]);
            $response->assertResponseStatus(422);

            /** Making sure user has email verification entry **/
            $emailVerificationRepository = new EmailVerificationRepository();
            $emailVerification = $emailVerificationRepository->findOneBy(["user" => $userResult["id"]]);
            $this->assertEquals( $emailVerification->getUser()->getId(), $userResult['id']);
            $this->assertEquals( $emailVerification->getVerified(), false);

            /** Test guest access with right id **/
            $response = $this->json('GET', "/contexts/guest/email-verification/" . $emailVerification->getId(),[]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['id'], $emailVerification->getId());
            $this->assertEquals($result['verified'], false);

            /** Test not validated **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $email, 'password' => $password]);
            $response->assertResponseStatus(403);

            /** Email verification endpoint **/
            $response = $this->json(
                'PUT', "/contexts/guest/email-verification/" . $emailVerification->getId(),
                [
                    "params" => [ "verified" => true ],
                    "options" => [ "simplifiedParams" => true ]
                ]
            );
            $result = $response->decodeResponseJson();
            $this->assertArrayHasKey('id', $result);

            /** Email verification verify true **/
            $response = $this->json('GET', "/contexts/guest/email-verification/" . $emailVerification->getId(),[]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['id'], $emailVerification->getId());
            $this->assertEquals($result['verified'], true);

            /** Test not validated **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $email, 'password' => $password]);
            $tokenResult = $response->decodeResponseJson();
            $response->assertResponseStatus(200);
            $this->assertArrayHasKey('token', $tokenResult);

            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group aclMiddleware
     * @return void
     * @throws Exception
     */
    public function testAdminAccess() :void
    {
        //$this->refreshApplication();

        /** Test guest access with wrong id **/
        $response = $this->json('GET', "/contexts/admin/email-verification/anyId!!!",[]);
        $response->assertResponseStatus(400);

        $response = $this->json('POST', '/auth/authenticate', ['email' => env('BASE_USER_EMAIL'), 'password' => env('BASE_USER_PASSWORD')]);
        $tokenResult = $response->decodeResponseJson();
        $response->assertResponseStatus(200);

        /** Test guest access with wrong id **/
        $response = $this->json('GET', "/auth/me", [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        $response->assertResponseStatus(200);

        /** Test guest access with wrong id **/
        $response = $this->json('GET', "/contexts/admin/email-verification", [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        $result = $response->decodeResponseJson();
        $response->assertResponseStatus(200);

        /** Test guest access with wrong id **/
        //$response = $this->json('GET', "/contexts/admin/email-verification/" . $result['result'][0]['id'], [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        //$response->assertResponseStatus(200);
    }
}
