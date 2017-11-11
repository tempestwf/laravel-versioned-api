<?php

namespace App\API\V1\Entities;

use App\Entities\Traits\Deletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping AS ORM;
use TempestTools\Moat\Contracts\HasRolesContract;
use TempestTools\Moat\Contracts\HasIdContract;
use TempestTools\Moat\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Common\Contracts\ExtractableContract;
use TempestTools\Common\Utility\ExtractorOptionsTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Entities\Traits\Authenticatable;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;
use TempestTools\Moat\Contracts\HasPermissionsContract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User extends EntityAbstract implements HasRolesContract, HasPermissionsContract, HasIdContract, ExtractableContract, AuthenticatableContract
{
    use Authenticatable, HasPermissionsOptimizedTrait, Deletable, ExtractorOptionsTrait;
	
	/**
	 * @ORM\Column(type="string", nullable=true, name="name")
	 * @var string $name
	 */
	protected $name;

    /**
     * @ORM\Column(type="string", nullable=true, name="address")
     * @var string $name
     */
    protected $address;

	/**
	 * @ORM\Column(type="string", nullable=true, name="job")
	 * @var string $job
	 */
	protected $job;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Permission", mappedBy="users", cascade={"persist"}, fetch="EXTRA_LAZY")
	 */
	private $permissions;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Role", mappedBy="users", cascade={"persist"}, fetch="EXTRA_LAZY")
	 */
	private $roles;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int $id
     */
    public $id;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId():?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId($id):User
    {
        $this->id = $id;

        return $this;
    }

    public function extractValues() : array
    {
        return [
            CommonArrayObjectKeyConstants::USER_KEY_NAME => [
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
     * @return Collection|NULL
     */
    public function getRoles():?Collection
    {
        return $this->roles;
    }

    /**
     * @return Collection|NULL
     */
    public function getPermissions():?Collection
    {
        return $this->permissions;
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
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return User
     */
    public function addRole(Role $role, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $role->addUser($this, true);
        }

        $this->roles[] = $role;
        return $this;
    }

    /**
     * @param Role $role
     * @param bool $preventLoop
     * @return User
     */
    public function removeRole(Role $role, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $role->removeUser($this, true);
        }
        $this->roles->removeElement($role);
        return $this;
    }


    /**
     * @param Permission $permission
     * @param bool $preventLoop
     * @return User
     */
    public function addPermission(Permission $permission, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $permission->addUser($this, true);
        }

        $this->permissions[] = $permission;
        return $this;
    }

    /**
     * @param Permission $permission
     * @param bool $preventLoop
     * @return User
     */
    public function removePermission(Permission $permission, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $permission->removeUser($this, true);
        }
        $this->permissions->removeElement($permission);
        return $this;
    }


    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getTTConfig(): array
    {
        /** @noinspection NullPointerExceptionInspection */
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false,
                    'settings'=>[
                        'validate'=>[ // Validates name and email and inherited by the rest of the config
                            'rules'=>[
                                'name'=>'required|min:2',
                                'email'=>'required|email',
                                'password'=>'required|min:8'
                            ],
                            'messages'=>NULL,
                            'customAttributes'=>NULL,
                        ],
                    ],
                    'toArray'=> [
                        'id'=>[],
                        'name'=>[],
                        'email'=>[],
                        'address'=>[],
                        'job'=>[],
                        'permissions'=>[],
                        'roles'=>[],
                    ]
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'settings'=>[
                        'validate'=>[ // Validates name and email and inherited by the rest of the config
                            'rules'=>[
                                'password'=>'min:8'
                            ],
                        ],
                    ],
                ],
                'delete'=>[
                    'extends'=>[':default:update'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:update']
                ],
            ],
            'user'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false,
                    'permissive'=>false,
                    'settings'=>[
                        'enforce'=>[
                            'id'=>$this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id']) // If you are a user, then you should only be able to alter your self
                        ],
                    ],
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'name'=>[ // Users can update their name
                            'permissive'=>true,
                        ],
                        'job'=>[ // Users can update their job
                            'permissive'=>true,
                        ],
                        'address'=>[ // Users can update their address
                            'permissive'=>true,
                        ],
                        'email'=>[ // Users can update their address
                            'permissive'=>true,
                        ],
                        'password'=>[ // Users can update their address
                            'permissive'=>true,
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'], // inherits all of user create, but turns on the allowed flag so now a user can update them selves
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>[':user:create'], // users can not delete them selves
                    'allowed'=>false
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            'admin'=>[ // admins can do the same thing as users except to any user, and they do not have update and delete restricted
                'create'=>[
                    'extends'=>[':user:create'],
                    'settings'=>[
                        'enforce'=>null
                    ],
                    'allowed'=>true,
                ],
                'update'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                ],
                'delete'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':admin:create']
                ],
            ],
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                    'permissive'=>true,
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'permissions'=>[ // Users can update their name
                            'permissive'=>true,
                        ],
                        'roles'=>[ // Users can update their job
                            'permissive'=>true,
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':superAdmin:create'],
                ],
                'delete'=>[
                    'extends'=>[':superAdmin:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':superAdmin:create']
                ],
            ],
            // Below here is for testing purposes only
            'testing'=>[
                'create'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'update'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'allowed'=>true,
                    'extends'=>[':default:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                ],
            ],
        ];
    }

}