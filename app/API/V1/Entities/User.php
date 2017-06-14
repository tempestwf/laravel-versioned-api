<?php

namespace App\API\V1\Entities;

use App\Entities\Traits\Deletable;
use App\Entities\User AS UserEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use LaravelDoctrine\ACL\Contracts\Permission;
use LaravelDoctrine\ACL\Roles\HasRoles;
use LaravelDoctrine\ACL\Mappings as ACL;
use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesContract;
use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionContract;
use LaravelDoctrine\ACL\Contracts\BelongsToOrganisations as BelongsToOrganisationsContract;
use LaravelDoctrine\ACL\Organisations\BelongsToOrganisation;
use TempestTools\AclMiddleware\Contracts\HasId;
use TempestTools\AclMiddleware\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Common\Contracts\Extractable;
use TempestTools\Common\Utility\ExtractorOptionsTrait;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends UserEntity implements HasRolesContract, HasPermissionContract, BelongsToOrganisationsContract, HasId, Extractable
{
	use HasPermissionsOptimizedTrait, HasRoles, BelongsToOrganisation, Deletable, ExtractorOptionsTrait;
	
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
    protected $permissions;

    /**
     * @ACL\BelongsToOrganisations
     * @var Organisation[]
     */
    protected $organisations;

    public function extractValues() : array
    {
        return [
            'userEntity' => [
                'id'=>$this->getId(),
                'name'=>$this->getName(),
                'job'=>$this->getJob(),
                'password'=>$this->getPassword(),
                'email'=>$this->getEmail(),
                'timeDeleted'=>$this->getTimeDeleted()
            ]
        ];
    }
	
	/**
	 * @return string
	 */
	public function getName():?String
	{
		return $this->name;
	}
	
	/**
	 * @param string $name
	 *
	 * @return User
	 */
	public function setName(string $name):User
	{
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getJob():?String
	{
		return $this->job;
	}
	
	/**
	 * @param string $job
	 *
	 * @return User
	 */
	public function setJob(string $job):User
	{
		$this->job = $job;
		
		return $this;
	}

    /**
     * @return PersistentCollection|\LaravelDoctrine\ACL\Contracts\Role[]
     */
    public function getRoles(): ?PersistentCollection
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
     * @return PersistentCollection
     */
    public function getPermissions():?PersistentCollection
    {
        return $this->permissions;
    }

    /**
     * @return PersistentCollection|Organisation[]
     */
    public function getOrganisations():?PersistentCollection
    {
        return $this->organisations;
    }

    /**
     * @param ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[] $roles
     * @return User
     */
    public function setRoles(ArrayCollection $roles):User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param string $address
     * @return User
     */
    public function setAddress(string $address): User
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}