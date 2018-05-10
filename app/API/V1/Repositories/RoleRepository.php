<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Permission;
use App\API\V1\Entities\Role;
use App\API\V1\Entities\User;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class RoleRepository extends Repository
{
	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = Role::class;

    /**
     * A convenience method to add permissions to a role
     * @param array $rolePermissions example:
     * [
     *  'name'=>'user',
     *  'permissions'=>['/user']
     * ]
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function buildPermissions(array $rolePermissions):void
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            foreach ($rolePermissions as $key => $permissions) {
                /** @var Role $role */
                $role = $this->findOneBy(['name'=>$key]);
                /** @var array $permissions */
                foreach ($permissions as $permissionName) {
                    $perm = new Permission();
                    $perm->setName($permissionName);
                    $role->addPermission($perm);
                }
                $em->persist($role);
            }
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }
    }

    /**
     * A convenience method to add permissions to a role
     * @param array $rolePermissions example:
     * * [
     *  'name'=>'user',
     *  'permissions'=>['/user']
     * ]
     * @param bool $delete
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function removePermissions(array $rolePermissions, bool $delete=false):void
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        $permissionRepo = $em->getRepository(Permission::class);
        try {
            foreach ($rolePermissions as $key => $permissions) {
                /** @var Role $role */
                $role = $this->findOneBy(['name'=>$key]);
                /** @var array $permissions */
                foreach ($permissions as $permissionName) {
                    /** @var Permission $perm */
                    $perm = $permissionRepo->findOneBy(['name'=>$permissionName]);
                    $role->removePermission($perm);
                    if ($delete === true) {
                        $em->remove($perm);
                    }
                }
                $em->persist($role);
            }
            $em->flush();
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
        }
    }

    /**
     * Set user's default role
     *
     * @param User $user
     * @param array $roles
     * @param boolean $transaction
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function addUserRoles(User $user, array $roles = ['user'], bool $transaction = true):void
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        if ($transaction === true) {
            $conn->beginTransaction();
        }

        try {
            foreach ($roles as $role) {
                if (!$user->hasRole(strtolower($role))) {
                    $eRole = $this->findOneBy(['name' => strtolower($role)]);
                    if ($eRole) {
                        $user->addRole($eRole);
                        $em->persist($user);
                    }
                }
            }
            if ($transaction === true) {
                $em->flush();
                $conn->commit();
            }
        } catch (\Exception $e) {
            if ($transaction === true) {
                $conn->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'read'=>[
                    'permissions'=>[
                        'allowed'=>false
                    ]
                ]
            ],
            'superAdmin'=>[
                'extends'=>[':default'],
                'read'=>[
                    'permissions'=>[
                        'allowed'=>true
                    ]
                ]
            ],
            // Below here is for testing purposes only
            'testing'=>[]
        ];
    }

}