<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\PersistentCollection;
use TempestTools\AclMiddleware\Contracts\Role as RoleContract;
use Doctrine\Common\Collections\ArrayCollection;
use TempestTools\AclMiddleware\Entity\HasPermissionsOptimizedTrait;
use TempestTools\Crud\Laravel\EntityAbstract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\RoleRepository")
 * @ORM\Table(name="roles")
 */
class Role extends EntityAbstract implements RoleContract
{
    use HasPermissionsOptimizedTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="UserToRole",
     *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")}
     * )
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\Permission", mappedBy="roles", cascade={"persist"})
     */
    private $permissions;


    /**
     * @return int
     */
    public function getId():Integer
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName():String
    {
        return $this->name;
    }

    /**
     * @param ArrayCollection $permissions
     * @return Role
     */
    public function setPermissions(ArrayCollection $permissions):Role
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getPermissions():PersistentCollection
    {
        return $this->permissions;
    }

    /**
     * @param string $name
     * @return Role
     */
    public function setName(string $name):Role
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'create'=>[ // the only thing we enforce on artists is the validator
                    'allowed'=>false,
                    'validator'=>[
                        'fields'=>[
                            'name'
                        ],
                        'rules'=>[
                            'name'=>'required|min:2',
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
            'superAdmin'=>[ // can do everything in default, and is allowed to do it when a super admin
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
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }
}