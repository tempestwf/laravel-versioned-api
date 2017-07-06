<?php

namespace App\API\V1\Entities;

use App\Entities\Traits\Deletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping AS ORM;
use TempestTools\AclMiddleware\Contracts\HasRoles as HasRolesContract;
use TempestTools\AclMiddleware\Contracts\HasPermissions as HasPermissionContract;
use TempestTools\AclMiddleware\Contracts\HasId;
use TempestTools\AclMiddleware\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Common\Contracts\Extractable;
use TempestTools\Common\Utility\ExtractorOptionsTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Entities\Traits\Authenticatable;
use TempestTools\Crud\Laravel\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends EntityAbstract implements HasRolesContract, HasPermissionContract, HasId, Extractable, AuthenticatableContract
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
     * ArrayCollection|App\API\V1\Entities\Album[]
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Album", inversedBy="users")
	 * @ORM\JoinTable(
	 *     name="AlbumToUser",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
	 * )
	 */
	protected $albums;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Permission", mappedBy="users", cascade={"persist"})
	 */
	private $permissions;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Role", mappedBy="users", cascade={"persist"})
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
        $this->albums = new ArrayCollection();
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
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return Collection|NULL
     */
    public function getAlbums(): ?Collection
    {
        return $this->albums;
    }

    /**
     * @param Album $album
     * @param bool $preventLoop
     * @return User
     */
    public function addAlbum(Album $album, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $album->addUser($this, true);
        }

        $this->albums[] = $album;
        return $this;
    }

    /**
     * @param Album $album
     * @param bool $preventLoop
     * @return User
     */
    public function removeAlbum(Album $album, bool $preventLoop = false): User
    {
        if ($preventLoop === false) {
            $album->removeUser($this, true);
        }

        $this->albums->removeElement($album);
        return $this;
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
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false,
                    'validator'=>[ // Validates name and email and inherited by the rest of the config
                        'rules'=>[
                            'name'=>'required|min:2',
                            'email'=>'required|email'
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
            'user'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false,
                    'permissive'=>false,
                    'enforce'=>[
                        'id'=>':userEntity:id' // If you are a user, then you should only be able to alter your self
                    ],
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'albums'=>[
                            'permissive'=>false,
                            'assign'=>[
                                'add'=>true,
                                'remove'=>true,
                            ],
                            'chain'=>[
                                'read'=>true
                            ]
                        ],
                        'name'=>[ // Users can update their name
                            'permissive'=>true,
                        ],
                        'job'=>[ // Users can update their job
                            'permissive'=>true,
                        ],
                        'address'=>[ // Users can update their address
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
                ]
            ],
            'admin'=>[ // admins can do the same thing as users except to any user, and they do not have update and delete restricted
                'create'=>[
                    'extends'=>[':user:create'],
                    'enforce'=>[],
                    'allowed'=>true,
                ],
                'update'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                ],
                'delete'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
                ]
            ],
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                ]
            ]
        ];
    }


}