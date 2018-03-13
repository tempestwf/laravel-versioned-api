<?php
namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\API\V1\Entities\User;
use App\API\V1\Traits\Entities\Blameable;
use TempestTools\Common\Entities\Traits\SoftDeleteable;

use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\SocializeUserRepository")
 * @ORM\Table(name="socialize_user")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SocializeUser extends EntityAbstract
{
    use Blameable, SoftDeleteable, IpTraceable, Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="socialize_id", type="string", nullable=true)
     */
    private $socializeId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(name="avatar_original", type="string", nullable=true)
     */
    private $avatarOriginal;

    /**
     * @ORM\Column(name="profile_url", type="string", nullable=true)
     */
    private $profileUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(name="refresh_token", type="string", nullable=true)
     */
    private $refreshToken;

    /**
     * @ORM\Column(name="expires_in", type="integer", nullable=true)
     */
    private $expiresIn;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="socialize")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * SocializeUser constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return SocializeUser
     */
    public function setId(int $id): SocializeUser
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSocializeId()
    {
        return $this->socializeId;
    }

    /**
     * @param mixed $socializeId
     */
    public function setSocializeId($socializeId): void
    {
        $this->socializeId = $socializeId;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return mixed
     */
    public function getAvatarOriginal()
    {
        return $this->avatarOriginal;
    }

    /**
     * @param mixed $avatarOriginal
     */
    public function setAvatarOriginal($avatarOriginal): void
    {
        $this->avatarOriginal = $avatarOriginal;
    }

    /**
     * @return mixed
     */
    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    /**
     * @param mixed $profileUrl
     */
    public function setProfileUrl($profileUrl): void
    {
        $this->profileUrl = $profileUrl;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return mixed
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param mixed $expiresIn
     */
    public function setExpiresIn($expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return User|null
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \App\API\V1\Entities\User $user
     * @return SocializeUser
     */
    public function setUser(User $user): SocializeUser
    {
        $this->user = $user;
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
                    'settings'=>[
                        'validate'=>[
                            'rules'=>[
                                'name'=>'required|min:2',
                            ],
                            'messages'=>NULL,
                            'customAttributes'=>NULL,
                        ],
                    ],
                    'toArray'=> [
                        'id'=>[],
                        'name'=>[],
                        'albums'=>[],
                    ]
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
            ],
            'admin'=>[
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
                'read'=>[ // Same as default create
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
            ],
            'superAdmin'=>[
                'create'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>[':admin:create'],
                    'allowed'=>true
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':admin:create'],
                    'allowed'=>true
                ],
            ],
            'guest'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false
                ],
                'read'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
            ]
        ];
    }
}