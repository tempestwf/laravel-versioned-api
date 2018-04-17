<?php

use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Repositories\UserRepository;
use Faker\Factory;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;

class RegistrationFlowTest extends CrudTestBaseAbstract
{
    protected $password = '441520435a0a2dac143af05b55f4b751';

    public function testEmailVerification():void
    {
        $this->refreshApplication();
        $em = $this->em();
        $conn = $em->getConnection();
        //$conn->beginTransaction();
        try {
            $generator = Factory::create();
            /** Register user via the guest endpoint **/
            $response = $this->json(
                'POST', '/contexts/guest/users',
                [
                    "params" => [
                        "name" => $generator->name,
                        "email" => $generator->safeEmail,
                        "password" => $this->password,
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

            /** Making sure user has email verification entry **/
            $emailVerificationRepository = new EmailVerificationRepository();
            $emailVerification = $emailVerificationRepository->findOneBy(["user" => $userResult["id"]]);
            $this->assertEquals( $emailVerification->getUser()->getId(), $userResult['id']);
            $this->assertEquals( $emailVerification->getVerified(), false);

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

            /** Making sure user has user has role entry of 'user' **/
            $userRepository = new UserRepository();
            $user = $userRepository->find($userResult["id"]);
            $this->assertEquals( $user->getId(), $userResult['id']);
            $this->assertEquals( $user->getRoles()[0]->getName(), 'user');

            /** Try to log in with wrong password **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $userResult["email"], 'password' => 'wrong password']);
            $response->assertResponseStatus(422);

            /** Try to log in with correct password **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $userResult["email"], 'password' => $this->password]);
            $result = $response->decodeResponseJson();
            $this->assertArrayHasKey('token', $result);

            /** Access me no token **/
            $response = $this->json('GET', '/auth/me', [],['HTTP_AUTHORIZATION'=>'Bearer ' . null]);
            $response->assertResponseStatus(400);

            /** Access me with token **/
            $response = $this->json('GET', '/auth/me', [],['HTTP_AUTHORIZATION'=>'Bearer ' . $result["token"]]);
            $result = $response->decodeResponseJson();
            $this->assertArrayHasKey('id', $result);
            $this->assertEquals( $user->getName(), $userResult["name"]);

            /** Leave no trace of test **/
            //$conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            //throw $e;
        }
    }
}