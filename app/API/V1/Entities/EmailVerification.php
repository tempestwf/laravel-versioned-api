<?php
namespace App\API\V1\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use phpDocumentor\Reflection\Types\Boolean;
use TempestTools\Common\Entities\Traits\Deletable;
use TempestTools\Common\Entities\Traits\Blameable;
use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Scribe\Doctrine\Events\GenericEventArgs;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

use App\API\V1\Entities\User;
/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\ArtistRepository")
 * @ORM\Table(name="email_verification")
 * @ORM\HasLifecycleCallbacks
 */
class EmailVerification extends EntityAbstract
{
    use Blameable, Deletable, IpTraceable, Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $verificationCode;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verified = false;

    /**
     * @ORM\OneToOne(targetEntity="App\API\V1\Entities\User", inversedBy="email_verification")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * User constructor.
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
     * @return EmailVerification
     */
    public function setId(int $id): EmailVerification
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    /**
     * @param string $verificationCode
     * @return EmailVerification
     */
    public function setVerificationCode(string $verificationCode): EmailVerification
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     * @return EmailVerification
     */
    public function verify(bool $verified): EmailVerification
    {
        $this->verified = $verified;
        return $this;
    }

    /**
     * @return \App\API\V1\Entities\User|null
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return EmailVerification
     */
    public function setUser(User $user): EmailVerification
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