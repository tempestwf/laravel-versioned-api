<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Repositories\RoleRepository;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\UnitTest\CrudTestBase;
use Faker\Factory;
use App\API\V1\Entities\User;

class AclMiddlewareTest extends CrudTestBase
{
    protected $password = 'Password00!';

    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group aclMiddleware
     * @return void
     * @throws Exception
     */
    public function testAclMiddleware():void
    {
        $generator = Factory::create();
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** Register user via the guest endpoint **/
            $response = $this->json(
                'POST', '/contexts/guest/users',
                [
                    "params" => [
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
                        'password' => $this->password,
                        'job' => $generator->jobTitle,
                        'address' => $generator->address,
                        'locale' => "en"
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
            $response->assertResponseStatus(200);

            /** Making sure user has user has role entry of 'user' **/
            $userRepository = new UserRepository();
            /** @var User $user */
            $user = $userRepository->find($userResult["id"]);
            $this->assertEquals( $user->getId(), $userResult['id']);
            $this->assertEquals( $user->getRoles()[0]->getName(), 'user');

            /** Login with wrong password **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => 'wrong_password']);
            $response->assertResponseStatus(401);

            /** Login with right password **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => $this->password]);
            $tokenResult = $response->decodeResponseJson();
            $response->assertResponseStatus(200);

            /** Free port acl **/
            $response = $this->json('GET', '/auth/me', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['email'], $user->getEmail());

            $data = [
                "params" => [
                    "name" => $generator->name
                ],
                "options" => [
                    "simplifiedParams" => true
                ]
            ];

            /** Posting artist as a quest **/
            $response = $this->json('POST', '/contexts/guest/artists', $data, ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
            $response->assertResponseStatus(405);

            /** Posting artist as a user **/
            $response = $this->json('POST', '/contexts/user/artists', $data, ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
            $result = $response->decodeResponseJson();
            $response->assertResponseStatus(500);

            /** Posting artist as a admin **/
            $response = $this->json('POST', '/contexts/admin/artists', $data, ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
            $response->assertResponseStatus(403);

            /** Posting artist as a super-admin **/
            $response = $this->json('POST', '/contexts/super-admin/artists', $data, ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
            $response->assertResponseStatus(404);
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
    public function testBaseUserACL() :void
    {
        $generator = Factory::create();

        $data = [
            "params" => [
                "name" => $generator->name
            ],
            "options" => [
                "simplifiedParams" => true
            ]
        ];

        /** Login with right password **/
        $response = $this->json('POST', '/auth/authenticate', ['email' => env('BASE_USER_EMAIL'), 'password' => env('BASE_USER_PASSWORD')]);
        $tokenResult = $response->decodeResponseJson();
        $response->assertResponseStatus(200);

        /** Free port acl **/
        $response = $this->json('GET', '/auth/me', [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        $result = $response->decodeResponseJson();
        $this->assertEquals($result['email'], env('BASE_USER_EMAIL'));

        /** Posting artist as a admin **/
        $response = $this->json('POST', '/contexts/admin/artists', $data, ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        $result = $response->decodeResponseJson();
        $response->assertResponseStatus(201);
        /** This means the records is to be inserted and admin has access **/
        //$this->assertEquals($result['message'], 'Transaction commit failed because the transaction has been marked for rollback only.');
    }
}
