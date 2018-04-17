<?php
namespace App\API\V1\Entities;

use App\API\V1\Traits\Entities\Blameable;
use App\Notifications\ResetPasswordNotification;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Common\Entities\Traits\SoftDeleteable;

use TempestTools\Common\Entities\Traits\IpTraceable;
use TempestTools\Common\Entities\Traits\Timestampable;
use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Laravel\Orm\NotifiableTrait;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;


/** @noinspection LongInheritanceChainInspection */
/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\PasswordResetRepository")
 * @ORM\Table(name="password_reset")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class PasswordReset extends EntityAbstract implements NotifiableEntityContract
{
    use Blameable, SoftDeleteable, IpTraceable, Timestampable, NotifiableTrait;

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
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return PasswordReset
     */
    public function setId(string $id): PasswordReset
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
     * @return PasswordReset
     */
    public function setVerified(bool $verified): PasswordReset
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
     * @return PasswordReset
     */
    public function setUser(User $user): PasswordReset
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
                    'allowed'=>true,
                    'notifications'=>[ // A list of arbitrary key names with the actual notifications that will be sent
                        'emailVerification'=>[
                            'notification'=>new ResetPasswordNotification($this),
                            'via'=>[
                                'mail'=>[
                                    'to'=>ArrayExpressionBuilder::closure(function () {
                                        return $this->getUser()->getEmail();
                                    })
                                ]
                            ]
                        ]
                    ]
                ],
                'update'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                    'permissive'=>true,
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