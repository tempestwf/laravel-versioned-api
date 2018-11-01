<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\API\V1\Traits\Entities\Blameable;
use phpDocumentor\Reflection\Types\Integer;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Common\Doctrine\Generator\SecureUniqueIdGenerator;

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

use App\Entities\Traits\GenerateRandomString;
use App\Entities\Traits\Authenticatable;
use TempestTools\Common\Entities\Traits\SoftDeleteable;
use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;


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
    use Authenticatable, Blameable, SoftDeleteable, IpTraceable, Timestampable, HasPermissionsOptimizedTrait, ExtractorOptionsTrait, NotifiableTrait, GenerateRandomString;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\TempestTools\Common\Doctrine\Generator\SecureUniqueIdGenerator")
     * @var string $id
     */
    public $id;

    /**
     * @ORM\Column(type="string", name="identification_key")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\TempestTools\Common\Doctrine\Generator\SecureUniqueIdGenerator")
     * @var string $identificationKey
     */
    public $identificationKey;

    /**
     * @ORM\Column(type="string", name="instadat_uuid", nullable=true)
     * @Gedmo\Versioned
     * @var string $thirdPartyUuid
     */
    protected $thirdPartyUuid;

    /**
     * @ORM\Column(type="string", name="first_name", nullable=true)
     * @Gedmo\Versioned
     * @var string $firstName
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", name="middle_initial", nullable=true)
     * @Gedmo\Versioned
     * @var string $middleInitial
     */
    protected $middleInitial;

    /**
     * @ORM\Column(type="string", name="last_name", nullable=true)
     * @Gedmo\Versioned
     * @var string $lastName
     */
    protected $lastName;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\Versioned
     * @var integer $age
     */
    private $age;

    /**
     * @ORM\Column(type="float", scale=2, nullable=false)
     * @Gedmo\Versioned
     * @var float $height
     */
    private $height;

    /**
     * @ORM\Column(type="float", scale=2, nullable=false)
     * @Gedmo\Versioned
     * @var float $weight
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", name="gender", nullable=false)
     * @Gedmo\Versioned
     * @var int $gender
     */
    protected $gender;

    /**
     * @ORM\Column(type="string", name="phone_number", nullable=true)
     * @Gedmo\Versioned
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\Versioned
     * @var int $lifestyle
     */
    private $lifestyle;

    /**
     * @ORM\Column(type="string", name="address", nullable=true)
     * @var string $name
     * @Gedmo\Versioned
     */
    protected $address;

    /**
     * @ORM\Column(type="string", name="locale", options={"default"="en"}, nullable=false)
     * @var string $locale
     * @Gedmo\Versioned
     */
    protected $locale;

    /**
     * @Gedmo\Slug(fields={"firstName", "middleInitial", "lastName"})
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Versioned
     * @var string $name
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", name="job", nullable=true)
     * @var string $job
     * @Gedmo\Versioned
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
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\LoginAttempt", mappedBy="user", cascade={"persist"})
     * @var LoginAttempt $loginAttempt
     */
    private $loginAttempt;

    /**
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\PasswordReset", mappedBy="user", cascade={"persist"})
     * @var PasswordReset $passwordReset
     */
    private $passwordReset;


    /**
     * @ORM\Column(type="boolean", nullable=false, name="locked")
     * @var boolean $locked
     * @Gedmo\Versioned
     */
    private $locked = false;

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
     * @return null|String
     */
    public function getId(): ?String
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return User
     */
    public function setId(string $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getIdentificationKey(): ?String
    {
        return $this->identificationKey;
    }

    /**
     * @param $identificationKey
     * @return User
     */
    public function setIdentificationKey(string $identificationKey): User
    {
        $this->identificationKey = $identificationKey;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getThirdPartyUuid(): ?String
    {
        return $this->thirdPartyUuid;
    }

    /**
     * @param $thirdPartyUuid
     * @return User
     */
    public function setThirdPartyUuid(string $thirdPartyUuid): User
    {
        $this->thirdPartyUuid = $thirdPartyUuid;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getFirstName(): ?String
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getMiddleInitial(): ?String
    {
        return $this->middleInitial;
    }

    /**
     * @param string $middleInitial
     * @return User
     */
    public function setMiddleInitial(string $middleInitial): User
    {
        $this->middleInitial = $middleInitial;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getLastName(): ?String
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return Int|null
     */
    public function getGender(): ?Int
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     * @return User
     */
    public function setGender(int $gender): User
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getPhoneNumber(): ?String
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber(string $phoneNumber): User
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?Float
    {
        return $this->height;
    }

    /**
     * @param float $height
     * @return User
     */
    public function setHeight(float $height): User
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getWeight(): ?Float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     * @return User
     */
    public function setWeight(float $weight): User
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?Int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return User
     */
    public function setAge(int $age): User
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLifestyle(): ?Int
    {
        return $this->lifestyle;
    }

    /**
     * @param int $lifestyle
     * @return User
     */
    public function setLifestyle(int $lifestyle): User
    {
        $this->lifestyle = $lifestyle;
        return $this;
    }

    /**
     * @return null|String
     */
    public function getSlug(): ?String
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return User
     */
    public function setSlug(string $slug): User
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getJob(): ?String
    {
        return $this->job;
    }

    /**
     * @param string $job
     *
     * @return User
     */
    public function setJob(string $job): User
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection|NULL
     */
    public function getRoles(): ?Collection
    {
        return $this->roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role)
    {
        $roles = [];
        foreach ($this->getRoles() as $r) {
            array_push($roles, $r->getName());
        }

        $result = \in_array($role, $roles, true);
        return $result;
    }

    /**
     * @return Collection|NULL
     */
    public function getPermissions(): ?Collection
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
     * @return EmailVerification
     */
    public function getEmailVerification(): EmailVerification
    {
        return $this->emailVerification;
    }

    /**
     * @param LoginAttempt $loginAttempt
     * @return User
     */
    public function setLoginAttempts(LoginAttempt $loginAttempt): User
    {
        $this->loginAttempt = $loginAttempt;
        return $this;
    }

    /**
     * @return LoginAttempt
     */
    public function getLoginAttempt(): LoginAttempt
    {
        return $this->loginAttempt;
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
     * @return bool|null
     */
    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     * @return User
     */
    public function setLocked(bool $locked): User
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Used by BasicDataExtractorMiddleware to retrieve the information about the currently logged in user.
     * @return array
     */
    public function extractValues(): array
    {
        return [
            CommonArrayObjectKeyConstants::USER_KEY_NAME => [
                'id'    => $this->getId(),
                'email' => $this->getEmail(),
                'firstName' => $this->getFirstName(),
                'middleInitial' => $this->getMiddleInitial(),
                'lastName' => $this->getLastName(),
                'age' => $this->getAge(),
                'weight' => $this->getWeight(),
                'height' => $this->getHeight(),
                'gender' => $this->getGender(),
                'phoneNumber' => $this->getPhoneNumber(),
                'lifestyle' => $this->getLifestyle(),
                'local' => $this->getLocale(),
                'job' => $this->getJob(),
                'createdAd' => $this->getCreatedAt(),
                'deletedAt' => $this->getDeletedAt()
            ]
        ];
    }

    /**
     * Gets triggered only on insert
     * Set identificationKey
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setIdentificationKey($this->generateRandomString(16));
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
            'default' => [
                'create' => [
                    'allowed' => false,
                    'permissive' => false,
                    'settings' => [
                        'validate' => [ // Validates name and email and inherited by the rest of the config
                            'rules' => [
                                'email' => 'required|email|max:255|unique:App\API\V1\Entities\User',
                                'firstName' => 'max:255',
                                'middleInitial' => 'max:1',
                                'lastName' => 'max:255',
                                'age' => 'required|numeric|integer|between:0,120',
                                'height' => 'required|numeric|between:0,999.99',
                                'weight' => 'required|numeric|between:0,999.99',
                                'lifestyle' => 'required',
                                'gender' => 'required',
                                'phoneNumber' => 'phone:AUTO,US',
                                'password' => 'required|regex:/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,}$/',
                                'locale' => 'required',
                            ],
                            'messages' => NULL,
                            'customAttributes' => NULL,
                        ],
                    ],
                    // When converted to an array, the following fields can be returned
                    'toArray' => [
                        'id' => [],
                        'identificationKey'=> [],
                        'email' => [],
                        'firstName' => [],
                        'middleInitial' => [],
                        'lastName' => [],
                        'age' => [],
                        'height' => [],
                        'weight' => [],
                        'lifestyle' => [],
                        'gender' => [],
                        'phoneNumber' => [],
                        'address' => [],
                        'job' => [],
                        'locale' => [],
                        'albums' => [],
                        'permissions' => [],
                        'roles' => [],
                    ],
                    'fields'=>[
                        'identificationKey'=> [ // identificationKey allowed
                            'permissive' => true,
                        ],
                        'firstName' => [ // firstName allowed
                            'permissive' => true,
                        ],
                        'middleInitial' => [ // middleInitial allowed
                            'permissive' => true,
                        ],
                        'lastName' => [ // lastName allowed
                            'permissive' => true,
                        ],
                        'job' => [ // job allowed
                            'permissive' => true,
                        ],
                        'address' => [ // address allowed
                            'permissive' => true,
                        ],
                        'email' => [ // email allowed
                            'permissive' => true,
                        ],
                        'password' => [ // password allowed
                            'permissive' => true,
                        ],
                        'locale' => [ // locale allowed
                            'permissive' => true,
                        ],
                        'passwordReset' => [
                            'permissive' => false,
                        ],
                    ],
                ],
                'update' => [
                    'extends' => [':default:create'],
                    'allowed' => false,
                    'permissive' => false,
                    'settings' => [
                        'validate' => [ // Validates name and email and inherited by the rest of the config
                            'rules' => [
                                'email' => 'email|max:255',
                                'firstName' => 'max:255',
                                'middleInitial' => 'max:1',
                                'lastName' => 'max:255',
                                'age' => 'numeric|integer|between:0,120',
                                'height' => 'numeric|between:0,999.99',
                                'weight' => 'numeric|between:0,999.99',
                                'phoneNumber' => 'phone:AUTO,US',
                                'password' => 'regex:/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,}$/',
                                'lifestyle' => '',
                                'gender' => '',
                                'locale' => '',
                            ],
                            'messages' => NULL,
                            'customAttributes' => NULL,
                        ],
                    ],
                    'fields'=>[
                        'identificationKey'=> [ // identificationKey allowed
                            'permissive' => false,
                        ],
                        'firstName' => [ // firstName allowed
                            'permissive' => true,
                        ],
                        'middleInitial' => [ // middleInitial allowed
                            'permissive' => true,
                        ],
                        'lastName' => [ // lastName allowed
                            'permissive' => true,
                        ],
                        'job' => [ // job allowed
                            'permissive' => true,
                        ],
                        'address' => [ // address allowed
                            'permissive' => true,
                        ],
                        'email' => [ // email allowed
                            'permissive' => false,
                        ],
                        'password' => [ // password allowed
                            'permissive' => false,
                        ],
                        'locale' => [ // locale allowed
                            'permissive' => true,
                        ],
                        'passwordReset' => [
                            'permissive' => false,
                        ],
                        'notifications' => [
                            'permissive' => false,
                        ]
                    ]
                ],
                'delete' => [
                    'extends' => [':default:create'],
                    'allowed' => false,
                    'permissive' => false
                ],
                'read' => [ // Same as default create
                    'extends' => [':default:create'],
                    'allowed' => false,
                    'permissive' => false
                ],
            ],
            'guest' => [
                'create' => [
                    'extends' => [':default:create'],
                    'allowed' => true,
                    'permissive' => true,
                    'settings' => [
                        // When a guest makes a new user we make a new email token for them. The id of the token is generated automatically is a unique randomly generated string
                        'mutate' => ArrayExpressionBuilder::closure(
                            function (array $params) {
                                $entity = $params['self'];
                                $emailToken = new EmailVerification();
                                $emailToken->setUser($entity);
                                $entity->setEmailVerification($emailToken);
                            }
                        )
                    ],
                    'notifications' => [ // A list of arbitrary key names with the actual notifications that will be sent
                        'emailVerification' => [
                            'notification' => new EmailVerificationNotification($this),
                            'via' => [
                                'mail' => [
                                    'to' => ArrayExpressionBuilder::closure(function () {
                                        return $this->getEmail();
                                    })
                                ]
                            ]
                        ]
                    ]
                ],
                'update' => [
                    'extends' => [':default:update'],
                    'allowed' => false,
                    'permissive' => false,
                ],
                'delete' => [
                    'extends' => [':default:delete'],
                    'allowed' => false,
                    'permissive' => false,
                ],
                'read' => [
                    'extends' => [':default:read'],
                    'allowed' => false,
                    'permissive' => false,
                ],
            ],
            // Configuration for when in user context. Extends default.
            'user' => [
                'create' => [
                    'extends' => [':default:create'],
                    'allowed' => false,
                    'permissive' => false,
                ],
                'update' => [
                    'extends' => [':guest:update'],
                    'allowed' => true,
                    'permissive' => true,
                    'settings' => [
                        // If you are in user context, then you should only be able to alter your self. We enforce that the userId match with currently logged in user.
                        'enforce' => [
                            'id' => $this->getArrayHelper()->parseArrayPath([CommonArrayObjectKeyConstants::USER_KEY_NAME, 'id'])
                        ],
                    ],
                    'fields' => [ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'albums' => [
                            'permissive' => true,
                            'assign' => [
                                'add' => true,
                                'remove' => true,
                                'addSingle' => true,
                                'removeSingle' => true,
                            ],
                            'chain' => [
                                'update' => true,
                                'read' => true
                            ]
                        ],
                    ],
                ],
                'delete' => [
                    'extends' => [':guest:delete'], // users can not delete them selves
                    'allowed' => false,
                    'permissive' => false
                ],
                'read' => [ // Same as default create
                    'extends' => [':guest:read'],
                    'allowed' => false,
                    'permissive' => false
                ],
            ],
            'admin' => [ // admins can do the same thing as users except to any user, and they do not have update and delete restricted
                'create' => [
                    'extends' => [':user:create'],
                    'allowed' => true,
                    'permissive' => false,
                    // We override the enforce to null so it is no longer enforced for admins.
                    'settings' => [
                        'enforce' => null
                    ],
                    'fields' => [ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'albums' => [
                            'permissive' => true,
                        ]
                    ],
                ],
                'update' => [
                    'extends' => [':user:update'],
                    'allowed' => true,
                    'permissive' => true,
                    'settings' => [
                      // If you are in user context, then you should only be able to alter your self. We enforce that the userId match with currently logged in user.
                        'enforce' => null,
                    ],
                    'fields' => [ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'password' => [
                            'permissive' => true,
                        ]
                    ],
                ],
                'delete' => [
                    'extends' => [':user:delete'],
                    'allowed' => true,
                    'permissive' => true
                ],
                'read' => [ // Same as default create
                    'extends' => [':user:read'],
                    'allowed' => true,
                    'permissive' => true
                ],
            ],
            'superAdmin' => [ // can do everything in default, and is allowed to do it when a super admin
                'create' => [
                    'extends' => [':admin:create'],
                    'allowed' => true,
                    'permissive' => true,
                    'fields' => [ // Users should only be able to add remove albums from them selves with no chaining to create, update or delete
                        'permissions' => [ // Users can update their name
                            'permissive' => true,
                        ],
                        'roles' => [ // Users can update their job
                            'permissive' => true,
                        ]
                    ],
                ],
                'update' => [
                    'extends' => [':admin:update'],
                ],
                'delete' => [
                    'extends' => [':admin:delete'],
                ],
                'read' => [ // Same as default create
                    'extends' => [':admin:read']
                ],
            ],
            // Below here is for testing purposes only
            'testing' => [
                'create' => [
                    'extends' => [':superAdmin:create'],
                    'allowed' => true,
                    'permissive' => true,
                    'settings' => [
                        'validate' => [ // Validates name and email and inherited by the rest of the config
                            'rules' => [
                                'email' => 'email|max:255',
                                'firstName' => 'max:255',
                                'middleInitial' => 'max:1',
                                'lastName' => 'max:255',
                                'age' => 'numeric|integer|between:0,120',
                                'height' => 'numeric|between:0,999.99',
                                'weight' => 'numeric|between:0,999.99',
                                'phoneNumber' => 'phone:AUTO,US',
                                'password' => 'regex:/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,}$/',
                                'lifestyle' => '',
                                'gender' => '',
                                'locale' => '',
                            ],
                            'messages' => NULL,
                            'customAttributes' => NULL,
                        ],
                    ],
                ],
                'update' => [
                    'extends' => [':default:update'],
                    'allowed' => true,
                    'permissive' => true,
                    'settings' => [
                        'validate' => [ // Validates name and email and inherited by the rest of the config
                            'rules' => [
                                'email' => 'email|max:255',
                                'firstName' => 'max:255',
                                'middleInitial' => 'max:1',
                                'lastName' => 'max:255',
                                'age' => 'numeric|integer|between:0,120',
                                'height' => 'numeric|between:0,999.99',
                                'weight' => 'numeric|between:0,999.99',
                                'phoneNumber' => 'phone:AUTO,US',
                                'password' => 'regex:/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z]).{8,}$/',
                                'lifestyle' => '',
                                'gender' => '',
                                'locale' => '',
                            ],
                            'messages' => NULL,
                            'customAttributes' => NULL,
                        ],
                    ],
                ],
                'delete' => [
                    'extends' => [':superAdmin:delete'],
                    'allowed' => true,
                    'permissive' => true
                ],
                'read' => [ // Same as default create
                    'extends' => [':superAdmin:read'],
                    'allowed' => true,
                    'permissive' => true
                ],
            ],
        ];
    }
}

