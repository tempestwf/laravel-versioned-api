<?php

use App\API\V3\Entities\Role;
use App\API\V3\Entities\User;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ACL\Permissions\Permission;

class DoctrineAclSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run()
    {
        /* included as an example of how to do this with raw sql:
            $rawSql = '
            INSERT INTO `permissions` (`name`) VALUES (\'auth/authenticate:POST\');
            INSERT INTO `permissions` (`name`) VALUES (\'auth/refresh:GET\');
            INSERT INTO `permissions` (`name`) VALUES (\'auth/me:GET\');

            INSERT INTO `roles` (`name`) VALUES (\'guest\');
            INSERT INTO `roles` (`name`) VALUES (\'user\');
            INSERT INTO `roles` (`name`) VALUES (\'admin\');
            INSERT INTO `roles` (`name`) VALUES (\'super-admin\');

            INSERT INTO `role_user` (`user_id`, `role_id`) VALUES (\'1\', \'4\');

            INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES (\'1\', \'1\');
            INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES (\'2\', \'2\');
            INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES (\'2\', \'3\');
        ';
        $this->em->getConnection()->exec($rawSql);
        $this->command->comment('Seeded Doctrine ACL Fixture Data');*/

        $authenticatePerm = new Permission('auth/authenticate:POST');
        $refreshPerm = new Permission('auth/refresh:GET');
        $mePerm = new Permission('auth/me:GET');

        $guestRole = new Role();
        $guestRole->setName('guest');
        $guestRole->setPermissions(new ArrayCollection([$authenticatePerm]));
        $userRole = new Role();
        $userRole->setName('user');
        $userRole->setPermissions(new ArrayCollection([$refreshPerm, $mePerm]));
        $adminRole = new Role();
        $adminRole->setName('admin');
        $superAdminRole = new Role();
        $superAdminRole->setName('super-admin');

        $baseUser = $this->em->getRepository(User::class)->find(1);
        $baseUser->setRoles(new ArrayCollection($guestRole, $userRole, $adminRole, $superAdminRole));


        $this->em->persist($authenticatePerm);
        $this->em->persist($refreshPerm);
        $this->em->persist($mePerm);

        $this->em->persist($guestRole);
        $this->em->persist($userRole);
        $this->em->persist($adminRole);
        $this->em->persist($superAdminRole);

        $this->em->persist($baseUser);

        $this->em->flush();

        $this->command->comment('Seeded Doctrine ACL Fixture Data');
    }
}
