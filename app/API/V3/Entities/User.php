<?php

namespace App\API\V3\Entities;

use App\Entities\Traits\Deletable;
use App\Entities\User AS UserEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use LaravelDoctrine\ACL\Contracts\Permission;
use LaravelDoctrine\ACL\Permissions\HasPermissions;
use LaravelDoctrine\ACL\Roles\HasRoles;
use LaravelDoctrine\ACL\Mappings as ACL;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesContract;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionContract;
use LaravelDoctrine\ACL\Contracts\BelongsToOrganisations as BelongsToOrganisationsContract;
use LaravelDoctrine\ACL\Organisations\BelongsToOrganisation;
/**
 * @ORM\Entity(repositoryClass="App\API\V3\Repositories\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends UserEntity implements HasRolesContract, HasPermissionContract, BelongsToOrganisationsContract
{
	use HasRoles, HasPermissions, BelongsToOrganisation, Deletable;
	
	/**
	 * @ORM\Column(name="name", type="string")
	 * @var string $name
	 */
	protected $name;

    /**
     * @ORM\Column(name="email", type="string")
     * @var string $name
     */
    protected $email;

    /**
     * @ORM\Column(name="address", type="string")
     * @var string $name
     */
    protected $address;

	/**
	 * @ORM\Column(name="job", type="string")
	 * @var string $job
	 */
	protected $job;

    /**
     * @ACL\HasRoles()
     * @var ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    protected $roles;

    /**
     * @ACL\HasPermissions
     */
    public $permissions;

    /**
     * @ACL\BelongsToOrganisations
     * @var Organisation[]
     */
    protected $organisations;
	
	/**
	 * @return string
	 */
	public function getName():String
	{
		return $this->name;
	}
	
	/**
	 * @param string $name
	 *
	 * @return User
	 */
	public function setName($name):User
	{
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getJob():String
	{
		return $this->job;
	}
	
	/**
	 * @param string $job
	 *
	 * @return User
	 */
	public function setJob($job):User
	{
		$this->job = $job;
		
		return $this;
	}

    /**
     * @return ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    public function getRoles(): ArrayCollection
    {
        return $this->roles;
    }

    /**
     * @param mixed $permissions
     * @return User
     */
    public function setPermissions(Permission $permissions):User
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

    /**
     * @return ArrayCollection|Organisation[]
     */
    public function getOrganisations():ArrayCollection
    {
        return $this->organisations;
    }

    /**
     * @param ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[] $roles
     * @return User
     */
    public function setRoles($roles):User
    {
        $this->roles = $roles;
        return $this;
    }
}