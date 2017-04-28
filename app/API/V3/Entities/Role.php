<?php

namespace App\API\V3\Entities;

use Doctrine\ORM\Mapping AS ORM;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Permissions\Permission;

/**
 * @ORM\Entity(repositoryClass="App\API\V3\Repositories\RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role implements RoleContract
{
    use HasPermissions;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ACL\HasPermissions
     */
    public $permissions;

    /**
     * @return int
     */
    public function getId():Integer
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName():String
    {
        return $this->name;
    }

    /**
     * @param mixed $permissions
     * @return Role
     */
    public function setPermissions(Permission $permissions):Role
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPermissions():ArrayCollection
    {
        return $this->permissions;
    }
}