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
     * @return int
     */
    public function getId():int
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
     * @param string $name
     * @return Permission
     */
    public function setName(string $name): Permission
    {
        $this->name = $name;
        return $this;
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
     * @return Permission
     */
    public function setUsers($users): Permission
    {
        $this->users = $users;
        return $this;
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
     * @return Permission
     */
    public function setRoles($roles): Permission
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Permission
     */
    public function addUser(User $user, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $user->addPermission($this, true);
        }

        $this->users[] = $user;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Permission
     */
    public function removeUser(User $user, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $user->removePermission($this, true);
        }
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return Permission
     */
    public function addRole(Role $role, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $role->addPermission($this, true);
        }

        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return Permission
     */
    public function removeRole(Role $role, bool $preventLoop = false): Permission
    {
        if ($preventLoop === false) {
            $role->removePermission($this, true);
        }
        $this->roles->removeElement($role);
        return $this;
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false,
                    'validator'=>[ // the only thing we enforce on artists is the validator
                        'rules'=>[
                            'name'=>'required|min:2',
                        ],
                        'messages'=>NULL,
                        'customAttributes'=>NULL,
                    ],
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                ]
            ],
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
            ]
        ];
    }
}
