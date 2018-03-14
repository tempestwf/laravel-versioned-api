<?php

use App\API\V1\Entities\Role;
use Faker\Factory;
use App\API\V1\Entities\User;
use App\API\V1\Entities\EmailVerification;
use TempestTools\Scribe\PHPUnit\CrudTestBaseAbstract;

class AclMiddlewareTest extends CrudTestBaseAbstract
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


    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group aclMiddleware
     * @return void
     * @throws Exception
     */
    public function testAclMiddlewareAllows():void
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
                ->verify(true);
            $em->persist($emailVerification);
            $em->flush();
            $conn->commit();

            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => 'password']);
            $result = $response->decodeResponseJson();

            $this->refreshApplication();

            $response = $this->json('GET', '/auth/me', ['token' => $result['token']], ['HTTP_AUTHORIZATION'=>'Bearer ' . $result['token']]);
            $result1 = $response->decodeResponseJson();
            $this->assertEquals($result1['email'], $user->getEmail());

            $this->refreshApplication();
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


    /**
     * Test that acl middle ware works in allowing some one to access an end point
     * @group aclMiddleware
     * @return void
     * @throws Exception
     */
    public function testAclMiddlewareDenies():void
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $user = $this->makeUser();
            $em->persist($user);
            $em->flush();

            $emailVerification = new EmailVerification();
            $emailVerification
                ->setVerificationCode('SampleVerificationCode')
                ->setUser($user)
                ->verify(true);
            $em->persist($emailVerification);
            $em->flush();
            $conn->commit();

            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => 'password']);
            $result = $response->decodeResponseJson();

            // This would not work with out storing to the db and then removing it after
            $this->refreshApplication();
            $response = $this->json('GET', '/auth/me', ['token' => $result['token']], ['HTTP_AUTHORIZATION'=>'Bearer ' . $result['token']]);
            $response->assertResponseStatus(403);
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
