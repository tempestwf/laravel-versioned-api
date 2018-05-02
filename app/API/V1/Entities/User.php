<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Entities\Traits\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\API\V1\Traits\Entities\Blameable;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Common\Entities\Traits\SoftDeleteable;
use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;

use TempestTools\Moat\Contracts\HasRolesContract;
use TempestTools\Moat\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Common\Contracts\ExtractableContract;
use TempestTools\Common\Utility\ExtractorOptionsTrait;
use App\Notifications\EmailVerificationNotification;
use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Laravel\Orm\NotifiableTrait;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;
use TempestTools\Moat\Contracts\HasPermissionsContract;


/** @noinspection LongInheritanceChainInspection */
/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User extends EntityAbstract implements HasRolesContract, HasPermissionsContract, ExtractableContract, AuthenticatableContract, NotifiableEntityContract
{
    use Authenticatable, Blameable, SoftDeleteable, IpTraceable, Timestampable, HasPermissionsOptimizedTrait, ExtractorOptionsTrait, NotifiableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int $id
     */
    public $id;

    /**
	 * @ORM\Column(type="string", nullable=true, name="name")
	 * @var string $name
     * @Gedmo\Versioned
	 */
	protected $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", nullable=true, name="address")
     * @var string $name
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=false, name="locale", options={"default"="en"})
     * @var string $locale
     */
    protected $locale;

	/**
	 * @ORM\Column(type="string", nullable=true, name="job")
	 * @var string $job
	 */
	protected $job;

	/**
     * ArrayCollection|App\API\V1\Entities\Album[]
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Album", inversedBy="users", fetch="EXTRA_LAZY")
	 * @ORM\JoinTable(
	 *     name="AlbumToUser",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
	 * )
	 */
	protected $albums;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Permission", mappedBy="users", cascade={"persist"}, fetch="EXTRA_LAZY")
	 */
	private $permissions;

	/**
	 * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Role", mappedBy="users", cascade={"persist"}, fetch="EXTRA_LAZY")
	 */
	private $roles;

    /**
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\SocializeUser", mappedBy="user")
     * @var SocializeUser $socialize
     */
    private $socialize;

    /**
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\EmailVerification", mappedBy="user", cascade={"persist"})
     * @var EmailVerification $emailVerification
     */
    private $emailVerification;

    /**
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\PasswordReset", mappedBy="user", cascade={"persist"})
     * @var PasswordReset $passwordReset
     */
    private $passwordReset;

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

    /**
     * Used by BasicDataExtractorMiddleware to retrieve the information about the currently logged in user.
     * @return array
     */
    public function extractValues() : array
    {
        return [
            CommonArrayObjectKeyConstants::USER_KEY_NAME => [
                'id'=>$this->getId(),
                'name'=>$this->getName(),
                'job'=>$this->getJob(),
                'password'=>$this->getPassword(),
                'email'=>$this->getEmail(),
                'deletedAt'=>$this->getDeletedAt()
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
     * @return null|String
     */
    public function getSlug():?String
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return User
     */
    public function setSlug(string $slug):User
    {
        $this->slug = $slug;

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

    public function hasRole()
    {
        return !!$this->getRoles();
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
     * @param string $locale
     * @return User
     */
    public function setLocale(string $locale): User
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
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
     * @param EmailVerification $emailVerification
     * @return User
     */
    public function setEmailVerification(EmailVerification $emailVerification): User
    {
        $this->emailVerification = $emailVerification;
        return $this;
    }

    /**
     * @return \App\API\V1\Entities\EmailVerification
     */
    public function getEmailVerification(): EmailVerification
    {
        return $this->emailVerification;
    }

    /**
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->emailVerification ? $this->emailVerification->getVerified() : false;
    }

    /**
     * @param \App\API\V1\Entities\SocializeUser $socialize
     * @return User
     */
    public function setSocialize(SocializeUser $socialize): User
    {
        $this->socialize = $socialize;
        return $this;
    }

    /**
     * @return \App\API\V1\Entities\SocializeUser
     */
    public function getSocialize(): SocializeUser
    {
        return $this->socialize;
    }

    /**
     * @return PasswordReset
     */
    public function getPasswordReset(): PasswordReset
    {
        return $this->passwordReset;
    }

    /**
     * @param PasswordReset $passwordReset
     */
    public function setPasswordReset(PasswordReset $passwordReset): void
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getTTConfig(): array
    {
        /** @noinspection NullPointerExceptionInspection */
        return [
            // Default context is the one that we fall back too, and other context inherit from.
            'default'=>[
                'create'=>[
                    'allowed'=>false,
                    'permissive'=>false,
                    'settings'=>[
                        'validate'=>[ // Validates name and email and inherited by the rest of the config
                            'rules'=>[
                                'name' => 'required|max:255',
                                'email' => 'required|email|max:255|unique:App\API\V1\Entities\User',
                                'password' => 'required|min:6',
                                'locale' => 'required',
                            ],
                            'messages'=>NULL,
                            'customAttributes'=>NULL,
                        ],
                    ],
                    // When converted to an array, the following fields can be returned
                    'toArray'=> [
                        'id'=>[],
                        'name'=>[],
                        'email'=>[],
                        'address'=>[],
                        'job'=>[],
                        'locale'=>[],
                        'albums'=>[],
                        'permissions'=>[],
                        'roles'=>[],
                    ],
                    'fields'=>[
                        'password'=>[ // password allowed
                            'permissive'=>true,
                        ],
                    ]

                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'settings'=>[
                        'validate'=>[ // The fields here are not required when doing an update, so change them to not required.
                            'rules'=>[
                                'name' => 'required|max:255',
                                'email' => 'required|email|max:255|unique:App\API\V1\Entities\User',
                                'password' => 'required|min:6',
                                'locale' => 'required',
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
            'guest'=>[
                'create'=>[
                    'allowed'=>true,
                    'permissive'=>true,
                    'extends'=>[':default:create'],
                    'settings'=>[
                        // When a guest makes a new user we make a new email token for them. The id of the token is generated automatically is a unique randomly generated string
                        'mutate'=>ArrayExpressionBuilder::closure(
                            function (array $params) {
                                $entity = $params['self'];
                                $emailToken = new EmailVerification();
                                $emailToken->setUser($entity);
                                $entity->setEmailVerification($emailToken);
                            }
                        )
                    ],
                    'name'=>[ // name allowed
                        'permissive'=>true,
                    ],
                    'job'=>[ // job allowed
                        'permissive'=>true,
                    ],
                    'address'=>[ // address allowed
                        'permissive'=>true,
                    ],
                    'email'=>[ // email allowed
                        'permissive'=>true,
                    ],
                    'password'=>[ // password allowed
                        'permissive'=>true,
                    ],
                    'locale'=>[ // locale allowed
                        'permissive'=>true,
                    ],
                    'notifications'=>[ // A list of arbitrary key names with the actual notifications that will be sent
                        'emailVerification'=>[
                            'notification'=>new EmailVerificationNotification($this),
                            'via'=>[
                                'mail'=>[
                                    'to'=>ArrayExpressionBuilder::closure(function () {
                                        return $this->getEmail();
                                    })
                                ]
                            ]
                        ]
                    ]
                ],
                'update'=>[
                    'allowed'=>false,
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'allowed'=>false,
                    'extends'=>[':default:create'],
                ],
                'read'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false,
                ],
            ],
            // Configuration for when in user context. Extends default.
            'user'=>[
                'create'=>[
                    'extends'=>[':guest:create'],
                    'allowed'=>false,
                    'permissive'=>false,
                    'settings'=>[
                        // If you are in user context, then you should only be able to alter your self. We enforce that the userId match with currently logged in user.
                        'enforce'=>[
                            'id'=>$this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id'])
                        ],
                    ],
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'albums'=>[
                            'permissive'=>false,
                            'assign'=>[
                                'add'=>true,
                                'remove'=>true,
                                'addSingle'=>true,
                                'removeSingle'=>true,
                            ],
                            'chain'=>[
                                'read'=>true
                            ]
                        ],
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'], // inherits all of user create, but turns on the allowed flag so now a user can update them selves
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'email'=>[ // Users can't update their email
                            'permissive'=>false,
                        ],
                    ],
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>[':user:create'], // users can not delete them selves
                    'allowed'=>false
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':guest:create']
                ],
            ],
            'admin'=>[ // admins can do the same thing as users except to any user, and they do not have update and delete restricted
                'create'=>[
                    'extends'=>[':user:create'],
                    'allowed'=>true,
                    'permissive'=>false,
                    // We override the enforce to null so it is no longer enforced for admins.
                    'settings'=>[
                        'enforce'=>null
                    ],
                    'fields'=>[ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'albums'=>[
                            'permissive'=>true,
                        ]
                    ],
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
                    'extends'=>[':admin:create'],
                    'allowed'=>true,
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
                    'extends'=>[':superAdmin:create'],
                ],
                'update'=>[
                    'allowed'=>true,
                    'extends'=>[':superAdmin:update'],
                ],
                'delete'=>[
                    'allowed'=>true,
                    'extends'=>[':superAdmin:delete'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':superAdmin:read'],
                    'allowed'=>true,
                ],
            ],
        ];
    }


}
