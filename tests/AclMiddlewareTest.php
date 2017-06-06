<?php

use App\API\V1\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Factory;
use App\API\V1\Entities\User;
use TempestTools\Common\Doctrine\Utility\MakeEmTrait;


class AclMiddlewareTest extends TestCase
{
    use MakeEmTrait;

    /**
     * Test that acl middle ware works
     *
     * @return void
     * @throws Exception
     */
    public function testAclMiddleware()
    {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $repo = $this->em->getRepository(Role::class);
            $userRole = $repo->findOneBy(['name' => 'user']);

            //$userRole = new Role();
            //$userRole->setName('user');

            $generator = Factory::create();
            $user = new User();

            $user
                ->setEmail($generator->safeEmail)
                ->setPassword('password')
                ->setName($generator->name)
                ->setJob($generator->jobTitle)
                ->setAddress($generator->address);
            $user->setRoles(new ArrayCollection([$userRole]));

            $em->persist($user);

            $em->flush();

            $response = $this->json('POST', '/auth/authenticate', ['email' => $user->getEmail(), 'password' => $user->getPassword()]);
            $result = $response->decodeResponseJson();

            $token = $result['token'];
            // This would not work with out storing to the db and then removing it after
            $conn->commit();
            $this->refreshApplication();

            $response = $this->json('GET', '/auth/me', ['token'=>$token], ['HTTP_AUTHORIZATION'=>'Bearer ' . $token]);
            $result = $response->decodeResponseJson();
            $this->assertEquals($result['email'], $user->getEmail());
            $em->remove($user);
            $em->flush();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
