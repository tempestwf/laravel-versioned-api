<?php

use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\Repositories\LoginAttemptRepository;
use App\API\V1\UnitTest\CrudTestBase;
use Faker\Factory;

class EmailVerificationTest extends CrudTestBase
{
    protected $password = 'Password00!';


    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group emailVerification
     * @return void
     * @throws Exception
     */
    public function testGuestAccess():void
    {
        $generator = Factory::create();
        $em = $this->em();
        try {
            $email = $generator->safeEmail;
            $password = $this->password;
            /** Register user via the guest endpoint **/
            $response = $this->json(
                'POST', '/contexts/guest/users',
                [
                    "params" => [
                        'email' => $email,
                        'firstName'=> $generator->firstName,
                        'middleInitial'=>'X',
                        'lastName'=> $generator->lastName,
                        'age' => $generator->randomNumber(2),
                        'gender' => 1,
                        'weight' => 210,
                        'height' => 180.34,
                        'phoneNumber' => "+1 757-571-2711",
                        'lifestyle' => 1,
                        'password' => $password,
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

            /** set token expired **/
            $emailVerificationCreatedDate = $emailVerification->getCreatedAt();
            $emailVerification->setCreatedAt($emailVerificationCreatedDate->modify('-' . env('EMAIL_VERIFICATION_TOKEN_LIFE_SPAN', 1440) . ' minutes'));
            $em->persist($emailVerification);
            $em->flush($emailVerification);

            /** Email verification endpoint **/
            $response = $this->json(
                'PUT', "/contexts/guest/email-verification/" . $emailVerification->getId(),
                [
                    "params" => [ "verified" => true ],
                    "options" => [ "simplifiedParams" => true ]
                ]
            );
            $response->assertResponseStatus(500);

            /** set token not expired **/
            $emailVerification->setCreatedAt($emailVerificationCreatedDate->modify('+' . env('EMAIL_VERIFICATION_TOKEN_LIFE_SPAN', 1440) . ' minutes'));
            $em->persist($emailVerification);
            $em->flush($emailVerification);

            /** Email verification endpoint **/
            $response = $this->json(
                'PUT', "/contexts/guest/email-verification/" . $emailVerification->getId(),
                [
                    "params" => [ "verified" => true ],
                    "options" => [ "simplifiedParams" => true ]
                ]
            );
            $response->assertResponseStatus(200);

            /** Email verification verify true **/
            $response = $this->json('GET', "/contexts/guest/email-verification/" . $emailVerification->getId(),[]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['id'], $emailVerification->getId());
            $this->assertEquals($result['verified'], true);

            /** Test login validated **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $email, 'password' => $password]);
            $tokenResult = $response->decodeResponseJson();
            $response->assertResponseStatus(200);
            $this->assertArrayHasKey('token', $tokenResult);

            $userRepo = new UserRepository();
            $user = $userRepo->find($userResult['id']);

            $loginAttemptRepository = new LoginAttemptRepository();
            $loginAttempt = $loginAttemptRepository->findOneBy(['user' => $user]);

            $emailVerification->setDeletedAt(new DateTime());
            $loginAttempt->setDeletedAt(new DateTime());
            $user->setDeletedAt(new DateTime());

            /** Delete EmailVerification **/
            $em->remove($emailVerification);
            $em->flush($emailVerification);

            /** Delete Login Attempt **/
            $em->remove($loginAttempt);
            $em->flush($loginAttempt);

            /** Delete User **/
            //$em->remove($user);
            //$em->flush($user);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group emailVerification
     * @return void
     * @throws Exception
     */
    public function testAdminAccess() :void
    {
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
        $response->assertResponseStatus(200);

        /** Test guest access with wrong id **/
        //$response = $this->json('GET', "/contexts/admin/email-verification/" . $result['result'][0]['id'], [], ['HTTP_AUTHORIZATION'=>'Bearer ' . $tokenResult['token']]);
        //$response->assertResponseStatus(200);
    }
}
