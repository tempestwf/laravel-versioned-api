<?php

use App\API\V1\Entities\Role;
use App\API\V1\Repositories\ArtistRepository;
use App\API\V1\UnitTest\CrudTestBase;

class RolesRepoTest extends CrudTestBase
{

    /**
     * @group RoleRepo
     * @throws Exception
     */
    public function testPermissionsConvenienceMethod () {
        $em = $this->em();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            /** @var ArtistRepository $artistRepo */
            $rolesRepo = $this->em->getRepository(Role::class);
            $permissionRepo = $this->em->getRepository(\App\API\V1\Entities\Permission::class);
            $rolesRepo->buildPermissions(
                [
                    'user'=>['test']
                ]
            );

            $result = $permissionRepo->findOneBy(['name'=>'test']);

            $this->assertNotNull($result);
            $rolesRepo->removePermissions(
                [
                    'user'=>['test']
                ],
                true
            );

            $result = $permissionRepo->findOneBy(['name'=>'test']);

            $this->assertNull($result);
            $conn->rollBack();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



}
