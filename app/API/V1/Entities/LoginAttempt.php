<?php

namespace App\API\V1\Entities;

use App\Notifications\LoginLockNotification;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\API\V1\Entities\User;

use App\API\V1\Traits\Entities\Blameable;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;
use TempestTools\Common\Entities\Traits\SoftDeleteable;
use TempestTools\Common\Entities\Traits\Timestampable;
use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Laravel\Orm\NotifiableTrait;
use TempestTools\Scribe\Laravel\Doctrine\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */
/** @noinspection PhpSuperClassIncompatibleWithInterfaceInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\LoginAttemptRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="login_attempt_user_unq", columns={"user_id"})})
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class LoginAttempt extends EntityAbstract implements NotifiableEntityContract
{
    use Blameable, SoftDeleteable, Timestampable, Blameable, NotifiableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned
     */
    private $count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\Versioned
     */
    private $fullLockCount;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     */
    private $trace;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="loginAttempt")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(length=45, nullable=true)
     * @Gedmo\Versioned
     */
    private $createdFromIp;

    /**
     * @var string
     * @Gedmo\IpTraceable(on="update")
     * @ORM\Column(length=45, nullable=true)
     * @Gedmo\Versioned
     */
    private $updatedFromIp;

    /**
     * LoginAttempt constructor.
     */
    public function __construct()
    {
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
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = $count;
    }

    /**
     * @return int|null
     */
    public function getFullLockCount(): ?int
    {
        return $this->fullLockCount;
    }

    /**
     * @param int $fullLockCount
     */
    public function setFullLockCount(int $fullLockCount)
    {
        $this->fullLockCount = $fullLockCount;
    }

    /**
     * @return mixed
     */
    public function getTrace()
    {
        return json_decode($this->trace);
    }

    /**
     * @param $trace
     */
    public function setTrace($trace)
    {
        $this->trace = json_encode($trace);
    }

    /**
     * @return User|null
     */
    public function getUser(): ? User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Sets createdFromIp.
     *
     * @param  string $createdFromIp
     * @return $this
     */
    public function setCreatedFromIp($createdFromIp)
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    /**
     * Returns createdFromIp.
     *
     * @return string
     */
    public function getCreatedFromIp()
    {
        return $this->createdFromIp;
    }

    /**
     * Sets updatedFromIp.
     *
     * @param  string $updatedFromIp
     * @return $this
     */
    public function setUpdatedFromIp($updatedFromIp)
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    /**
     * Returns updatedFromIp.
     *
     * @return string
     */
    public function getUpdatedFromIp()
    {
        return $this->updatedFromIp;
    }

    /**
     * On an entity with HasLifecycleCallbacks it will run the special features of tt entities before persist
     *
     * @ORM\PreUpdate
     * @throws \RuntimeException
     */
    public function ttPrePersist():void
    {
        //$arrayHelper = $this->getConfigArrayHelper();
        //if ($arrayHelper !== NULL) {
        /** @noinspection PhpParamsInspection */
        //$arrayHelper->ttPrePersist($this);
        //}
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
                    'toArray'=> [
                        'id'=>[],
                        'count'=>[],
                        'user'=>[],
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
            'guest'=>[ // can do everything in default, and is allowed to do it when a super admin
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
                    'extends'=>[':default:create']
                ],
            ],
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                    'notifications'=>[ // A list of arbitrary key names with the actual notifications that will be sent
                        'fullLockCount'=>[
                            'notification'=>new LoginLockNotification($this),
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
                    'notifications'=>[ // A list of arbitrary key names with the actual notifications that will be sent
                        'fullLockCount'=>[
                            'notification'=>new LoginLockNotification($this),
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
                'delete'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true
                ],
                'read'=>[ // Same as default create
                    'extends'=>[':default:create']
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
                    'extends'=>[':default:create']
                ],
            ],
        ];
    }
}
