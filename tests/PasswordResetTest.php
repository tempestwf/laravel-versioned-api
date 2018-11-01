<?php
/**
 * Created by PhpStorm.
 * User: monxe
 * Date: 19/04/2018
 * Time: 8:31 PM
 */

use App\API\V1\Entities\PasswordReset;
use App\API\V1\Repositories\EmailVerificationRepository;
use App\API\V1\Repositories\PasswordResetRepository;
use App\API\V1\Repositories\UserRepository;
use App\API\V1\UnitTest\CrudTestBase;
use Faker\Factory;

class PasswordResetTest extends CrudTestBase
{
    protected $password = 'Password00!';

    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group passwordReset
     * @return void
     * @throws Exception
     */
    public function testRequestPasswordReset():void
    {
        $generator = Factory::create();
        $em = $this->em();
        try {
            $email = $generator->email;
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
                        "g-recaptcha-response-omit" => env('GOOGLE_RECAPTCHA_SKIP_CODE'), /** skipping recaptcha with a key **/
                        "simplifiedParams" => true
                    ]
                ]
            );
            $userResult = $response->decodeResponseJson();
            $this->assertArrayHasKey('id', $userResult);

            /** Test password reset with email not verified yet **/
            $response = $this->json(
                'POST', "/contexts/guest/password-reset",
                [
                    "params" => ["user" => ["id" => $userResult['id']]],
                    "options" => [ "simplifiedParams" => true ]
                ]
            );
            $response->assertResponseStatus(500);

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

            /** Test password reset with email verified **/
            $response = $this->json(
                'POST', "/contexts/guest/password-reset",
                [
                    "options" => [
                        "simplifiedParams" => true,
                        "email" => $email
                    ]
                ]
            );
            $response->assertResponseStatus(201);

            /** Get password reset **/
            $passwordResetRepo = new PasswordResetRepository();

            /** @var PasswordReset $passwordReset */
            $passwordReset = $passwordResetRepo->findOneBy(["user" => $userResult['id']]);

            /** set token expired **/
            $passwordResetCreatedDate = $passwordReset->getCreatedAt();
            $passwordReset->setCreatedAt($passwordResetCreatedDate->modify('-' . env('PASSWORD_RESET_TOKEN_LIFE_SPAN', 1440) . ' minutes'));
            $em->persist($passwordReset);
            $em->flush($passwordReset);

            /** Change password on expired password reset token **/
            $response = $this->json(
                'PUT', '/contexts/guest/password-reset/' . $passwordReset->getId(),
                [
                    "params" => ["verified" => true] ,
                    "options" => [
                        "password"=>"Password00!",
                        "simplifiedParams" => true
                    ]
                ]
            );
            $response->assertResponseStatus(500);

            /** set token not expired **/
            $passwordReset->setCreatedAt($passwordResetCreatedDate->modify('+' . env('PASSWORD_RESET_TOKEN_LIFE_SPAN', 1440) . ' minutes'));
            $em->persist($passwordReset);
            $em->flush($passwordReset);

            /** Change password without set password **/
            $response = $this->json(
                'PUT', '/contexts/guest/password-reset/' . $passwordReset->getId(),
                [
                    "params" => ["verified" => true],
                    "options" => [
                        "simplifiedParams" => true
                    ]
                ]
            );
            $response->assertResponseStatus(500);

            /** Change password **/
            $response = $this->json(
                'PUT', '/contexts/guest/password-reset/' . $passwordReset->getId(),
                [
                    "params" => ["verified" => true] ,
                    "options" => [
                        "password"=>"Password00!",
                        "simplifiedParams" => true
                    ]
                ]
            );
            $response->assertResponseStatus(200);

            /** Change password again **/
            $response = $this->json(
                'PUT', '/contexts/guest/password-reset/' . $passwordReset->getId(),
                [
                    "params" => ["verified" => true],
                    "options" => [
                        "password"=>"Password00!",
                        "simplifiedParams" => true
                    ]
                ]
            );
            $response->assertResponseStatus(500);

            /** Test login validated **/
            $response = $this->json('POST', '/auth/authenticate', ['email' => $email, 'password' => 'Password00!']);
            $tokenResult = $response->decodeResponseJson();
            $response->assertResponseStatus(200);
            $this->assertArrayHasKey('token', $tokenResult);



            /** Delete PasswordReset **/
            $passwordReset = $passwordResetRepo->find($passwordReset->getId());
            $passwordReset->setDeletedAt(new DateTime());
            $em->remove($passwordReset);
            $em->flush($passwordReset);

            /** Delete EmailVerification **/
            $emailVerification->setDeletedAt(new DateTime());
            $em->remove($emailVerification);
            $em->flush($emailVerification);

            /** Delete User **/
            $userRepo = new UserRepository();
            $user = $userRepo->find($userResult['id']);
            $user->setDeletedAt(new DateTime());
            $em->remove($user);
            $em->flush($user);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
