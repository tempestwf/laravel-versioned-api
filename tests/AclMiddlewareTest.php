<?php

use App\API\V1\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Factory;
use App\API\V1\Entities\User;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;
use Tymon\JWTAuth\JWTAuth;


class AclMiddlewareTest extends TestCase
{
    use MakeEmTrait;

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
            ->setAddress($generator->address);
        return $user;
    }

    /**
     * Test that acl middle ware works in allowing some one to access an end point
     *
     * @return void
     * @throws Exception
     */
    public function testAclMiddlewareAllows()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $repo = $this->em->getRepository(Role::class);
            $userRole = $repo->findOneBy(['name' => 'user']);

            $user = $this->makeUser();

            $user->setRoles(new ArrayCollection([$userRole]));

            $em->persist($user);

            //$em->flush();

            //$response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => $user->getPassword()]);
            //$result = $response->decodeResponseJson();
            $auth = App::make(JWTAuth::class);
            $result = $auth->attempt(['email' => $user->getEmail(), 'password' => $user->getPassword()]);

            $token = $result['token'];
            // This would not work with out storing to the db and then removing it after
            //$conn->commit();
            //$this->refreshApplication();
            //$conn->beginTransaction();
            $response = $this->json('GET', '/auth/me', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['email'], $user->getEmail());
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
     *
     * @return void
     * @throws Exception
     */
    public function testAclMiddlewareDenies()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $user = $this->makeUser();

            $em->persist($user);

            $em->flush();

            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => $user->getPassword()]);
            $result = $response->decodeResponseJson();

            $token = $result['token'];
            // This would not work with out storing to the db and then removing it after
            $conn->commit();
            $this->refreshApplication();
            $conn->beginTransaction();
            $response = $this->json('GET', '/auth/me', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);

            $response->assertResponseStatus(403);
            $em->remove($user);
            $em->flush();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
