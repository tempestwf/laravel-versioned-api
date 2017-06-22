<?php

namespace App\API\V1\Entities;

use App\Entities\Traits\Deletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use Hash;
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
use TempestTools\Common\Laravel\Utility\Extractor;
use TempestTools\Common\Utility\ExtractorOptionsTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Entities\Traits\Authenticatable;
use TempestTools\Crud\Laravel\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends EntityAbstract implements HasRolesContract, HasPermissionContract, BelongsToOrganisationsContract, HasId, Extractable, AuthenticatableContract
{
    use Authenticatable, HasPermissionsOptimizedTrait, HasRoles, BelongsToOrganisation, Deletable, ExtractorOptionsTrait;
	
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
        $this->organisations = new ArrayCollection();
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
     * @return ArrayCollection|PersistentCollection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return ArrayCollection|PersistentCollection|Organisation[]
     */
    public function getOrganisations()
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

    /**
     * @return ArrayCollection|Album[]
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param Album $album
     * @param bool $preventLoop
     */
    public function addAlbum(Album $album, bool $preventLoop = false)
    {
        if ($preventLoop === false) {
            $album->addUser($this, true);
        }

        $this->albums[] = $album;
    }

    /**
     * @param Album $album
     * @param bool $preventLoop
     */
    public function removeAlbum(Album $album, bool $preventLoop = false)
    {
        if ($preventLoop === false) {
            $album->removeUser($this, true);
        }

        $this->albums->removeElement($album);
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
                    'validator'=>[
                        'fields'=>[
                            'name',
                            'email'
                        ],
                        'rules'=>[
                            'name'=>'required|min:2',
                            'email'=>'required|email'
                        ],
                        'messages'=>NULL,
                        'customAttributes'=>NULL,
                    ],
                    'fields'=>[
                        'password'=>[
                            'permissive'=>true,
                            'mutate'=>function (){
                                /** @noinspection NullPointerExceptionInspection */
                                return Hash::make($this->getArrayHelper()->parseArrayPath([Extractor::EXTRACTOR_KEY_NAME, 'config', 'hashSecret']));
                            }
                        ],
                    ]
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
                    'allowed'=>true,
                    'permissive'=>false,
                    'enforce'=>[
                        'id'=>':userEntity:id'
                    ],
                    'fields'=>[
                        'albums'=>[
                            'permissive'=>false,
                            'assign'=>[
                                'add'=>true,
                                'remove'=>true,
                            ],
                            'chain'=>[
                                'read'=>true
                            ]
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'],
                ],
                'delete'=>[
                    'extends'=>[':user:create'],
                    'allowed'=>false
                ]
            ],
            'superAdmin'=>[
                'create'=>[
                    'extends'=>[':user:create'],
                    'permissive'=>true,
                    'enforce'=>[],
                    'fields'=>[
                        'albums'=>[
                            'permissive'=>true
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'],
                ],
                'delete'=>[
                    'extends'=>[':user:create']
                ]
            ]
        ];
    }
}