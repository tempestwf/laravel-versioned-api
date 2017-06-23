<?php

namespace App\API\V1\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\AclMiddleware\Contracts\Permission as PermissionContract;
use TempestTools\Crud\Laravel\EntityAbstract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\PermissionRepository")
 */
class Permission extends EntityAbstract implements PermissionContract
{
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
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="UserToPermission",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Role", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="RoleToPermission",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}
