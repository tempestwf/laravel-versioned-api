<?php

use App\API\V1\Entities\User;
use App\API\V1\Repositories\UserRepository;
Use App\API\V1\Entities\Role;
use App\API\V1\Entities\EmailVerification;
use App\API\V1\Entities\Permission;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/** @var EntityManagerInterface $em */
	protected $em;
	
	/**
	 * DatabaseSeeder constructor.
	 *
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
	public function run()
	{
        $conn = $this->em->getConnection();
        try {
            $userRepo = new UserRepository();
            $baseUser = $userRepo->findOneBy(['email' => env('BASE_USER_EMAIL')]);
            /** if no $baseUser then run seed */
            if (!$baseUser) {
                $conn->beginTransaction();
                /** Auth, Permissions and Role **/
                $authenticatePerm = new Permission();
                $authenticatePerm->setName('auth/authenticate:POST');
                $refreshPerm = new Permission();
                $refreshPerm->setName('auth/refresh:GET');
                $mePerm = new Permission();
                $mePerm->setName('auth/me:GET');

                $userRole = new Role();
                $userRole->setName('user');
                $userRole->addPermission($refreshPerm);
                $userRole->addPermission($mePerm);
                //$userRole->setPermissions(new ArrayCollection([$refreshPerm, $mePerm]));
                $adminRole = new Role();
                $adminRole->setName('admin');
                $superAdminRole = new Role();
                $superAdminRole->setName('super-admin');

                $this->em->persist($authenticatePerm);
                $this->em->persist($refreshPerm);
                $this->em->persist($mePerm);
                $this->em->persist($userRole);
                $this->em->persist($adminRole);
                $this->em->persist($superAdminRole);
                $this->em->flush();
                $conn->commit();

                $conn->beginTransaction();
                $generator = Factory::create();
                /* Init base user */
                $user = new User();
                $user
                    ->setIdentificationKey(bin2hex(random_bytes(16)))
                    ->setInstadatUUid(bin2hex(random_bytes(16)))
                    ->setEmail(env('BASE_USER_EMAIL'))
                    ->setPassword(env('BASE_USER_PASSWORD'))
                    ->setFirstName(env('BASE_FIRST_NAME'))
                    ->setMiddleInitial(env('BASE_MIDDLE_INITIAL'))
                    ->setLastName(env('BASE_LAST_NAME'))
                    ->setAge(32)
                    ->setHeight(180.34)
                    ->setWeight(210)
                    ->setGender(1)
                    ->setLifestyle(1)
                    ->setPhoneNumber('+1 999-999-9999')
                    ->setJob($generator->jobTitle)
                    ->setLocale('en')
                    ->setAddress($generator->address);

                $this->em->persist($user);

                $emailVerification = new EmailVerification();
                $emailVerification
                    ->setUser($user)
                    ->setVerified(true);

                $this->em->persist($emailVerification);

                $this->em->flush();
                $conn->commit();

                $conn->beginTransaction();
                /** @var User $baseUser **/
                $baseUser = $this->em->getRepository(User::class)->find($user->getId());
                $baseUser->addRole($userRole);
                $baseUser->addRole($adminRole);
                $baseUser->addRole($superAdminRole);

                $this->em->persist($baseUser);
                $this->em->flush();
                $conn->commit();

                $conn->beginTransaction();
                $repo = $this->em->getRepository(Role::class);
                $repo->buildPermissions([
                    'user'=>[
                        '/contexts/user/albums',
                        '/contexts/user/albums/{album}',
                        '/contexts/user/artists',
                        '/contexts/user/artists/{artist}',
                        '/contexts/user/users',
                        '/contexts/user/users/{user}',
                        '/contexts/user/users/{user}/albums',
                        '/contexts/user/email-verification/{id}',
                    ],
                    'admin'=>[
                        '/contexts/admin/albums',
                        '/contexts/admin/albums/{album}',
                        '/contexts/admin/artists',
                        '/contexts/admin/artists/{artist}',
                        '/contexts/admin/users',
                        '/contexts/admin/users/{user}',
                        '/contexts/admin/users/{user}/albums',
                        '/contexts/admin/email-verification',
                        '/contexts/admin/email-verification/{id}',
                    ],
                    'super-admin'=>[
                        '/contexts/super-admin/users',
                        '/contexts/super-admin/users/{user}',
                        '/contexts/super-admin/permissions',
                        '/contexts/super-admin/roles',
                        '/contexts/super-admin/permissions/{permission}',
                        '/contexts/super-admin/roles/{role}',
                    ]
                ]);
                $conn->commit();
            }
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}