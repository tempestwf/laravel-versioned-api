<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Mappings as ACL;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\RoleRepository")
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
     * @ORM\Column(type="string", nullable=true)
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
     * @param ArrayCollection $permissions
     * @return Role
     */
    public function setPermissions(ArrayCollection $permissions):Role
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getPermissions():PersistentCollection
    {
        return $this->permissions;
    }

    /**
     * @param string $name
     * @return Role
     */
    public function setName(string $name):Role
    {
        $this->name = $name;
        return $this;
    }
}