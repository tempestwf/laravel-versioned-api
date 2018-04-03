<?php

use App\API\V1\Entities\Role;
use Faker\Factory;
use App\API\V1\Entities\User;
use App\API\V1\Entities\EmailVerification;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;

class RegistrationFlowTest extends CrudTestBaseAbstract
{
    /**
     * @return User
     */
    protected function makeUser(): User
    {
        $generator = Factory::create();
        $user = new User();
        $user
            ->setEmail($generator->safeEmail)
            ->setPassword('password')
            ->setName($generator->name)
            ->setJob($generator->jobTitle)
            ->setLocale('en')
            ->setAddress($generator->address);
        return $user;
    }

    public function testEmailVerification():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $repo = $this->em->getRepository(Role::class);
            /** @var Role $userRole */
            $userRole = $repo->findOneBy(['name' => 'user']);
            $user = $this->makeUser();
            $user->addRole($userRole);
            $em->persist($user);
            $em->flush();

            $emailVerification = new EmailVerification();
            $emailVerification
                ->setVerificationCode('SampleVerificationCode')
                ->setUser($user)
                ->verify(false);
            $em->persist($emailVerification);
            $em->flush();
            $conn->commit();

            $conn->beginTransaction();

            $verificationCode = base64_encode($user->getEmail() . '_wrong code here');
            $response = $this->json('GET', '/activate/' . $verificationCode);
            /** Fail Activation wih wrong activation code **/
            $response->assertResponseStatus(401);

            $this->refreshApplication();

            $verificationCode = base64_encode($user->getEmail() . '_' . $emailVerification->getVerificationCode());
            $response = $this->json('GET', '/activate/' . $verificationCode);
            /** Successfully activated **/
            $response->assertResponseStatus(200);

            $this->refreshApplication();

            $userRole = $user->getRoles();
            /** Make sure the new user only has 1 role **/
            $this->assertEquals(count($userRole), 1);
            /** Make sure the new user only has user role **/
            $this->assertEquals($userRole[0]->getName(), 'user');

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }

        $conn->beginTransaction();
        try {
            $em->remove($user);
            $em->flush();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}