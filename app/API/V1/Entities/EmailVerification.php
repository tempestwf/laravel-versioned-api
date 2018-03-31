<?php
namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\API\V1\Traits\Entities\Blameable;
use TempestTools\Common\Entities\Traits\SoftDeleteable;

use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\EmailVerificationRepository")
 * @ORM\Table(name="email_verification")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EmailVerification extends EntityAbstract
{
    use Blameable, SoftDeleteable, IpTraceable, Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="TempestTools\Common\Doctrine\Generator\SecureUniqueIdGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $verified = false;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="emailVerification")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * @return User|null
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
                    'allowed'=>false, // all actions off by default, they get turned on for guests and hire levels
                    'toArray'=> [
                        'id'=>[]
                    ],
                    'fields'=>[
                        'verified'=>[ // Can't create this with verified
                            'permissive'=>false,
                        ]
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
            'guest'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                    'fields'=>[
                        'verified'=>[ // A guest can set the token to verified because they won't be able to find the token with out having received it in their email
                            'permissive'=>true,
                        ]
                    ]
                ],
                'delete'=>[
                    'extends'=>[':default:create'],
                ],
                'read'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
            ],
            'admin'=>[
                'create'=>[
                    'extends'=>[':guest:create'],
                ],
                'update'=>[
                    'extends'=>[':guest:update'],
                ],
                'delete'=>[ // It takes admin access to revoke a token in order to reissue it
                    'extends'=>[':guest:delete'],
                    'allowed'=>true
                ],
                'read'=>[
                    'extends'=>[':guest:read'],
                ],
            ]
        ];
    }
}