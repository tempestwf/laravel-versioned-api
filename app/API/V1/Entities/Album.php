<?php
namespace App\API\V1\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use TempestTools\Crud\Laravel\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\AlbumRepository")
 * @ORM\Table(indexes={@ORM\Index(name="name_idx", columns={"name"}),@ORM\Index(name="releaseDate_idx", columns={"release_date"})})
 */
class Album extends EntityAbstract
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="release_date")
     */
    private $releaseDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\API\V1\Entities\Artist", inversedBy="albums", cascade={"persist"})
     * @ORM\JoinColumn(name="artist_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $artist;

    /**
     * @ORM\ManyToMany(targetEntity="App\API\V1\Entities\User", mappedBy="albums")
     * 
     */
    private $users;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        parent::__construct();
    }


    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Album
     */
    public function addUser(User $user, bool $preventLoop = false): Album
    {
        if ($preventLoop === false) {
            $user->addAlbum($this, true);
        }

        $this->users[] = $user;
        return $this;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     * @return Album
     */
    public function removeUser(User $user, bool $preventLoop = false): Album
    {
        if ($preventLoop === false) {
            $user->removeAlbum($this, true);
        }
        $this->users->removeElement($user);
        return $this;
    }

    /**
     * @return Artist
     */
    public function getArtist(): Artist
    {
        return $this->artist;
    }

    /**
     * @param Artist $artist
     * @return Album
     */
    public function setArtist(Artist $artist): Album
    {
        $this->artist = $artist;
        return $this;
    }

    /**
     * @return string
     */
    public function getReleaseDate():string
    {
        return $this->releaseDate;
    }

    /**
     * @param string $releaseDate
     * @return Album
     */
    public function setReleaseDate(string $releaseDate): Album
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Album
     */
    public function setName(string $name): Album
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getId():int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Album
     */
    public function setId(int $id): Album
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
                'create'=>[
                    'allowed'=>false, // by default this is not allowed
                    'validator'=>[ // Add a validator that will be inherited by all other configs
                        'rules'=>[
                            'name'=>'required|min:2',
                            'releaseDate'=>'required|date'
                        ],
                        'messages'=>NULL,
                        'customAttributes'=>NULL,
                    ],
                ],
                'update'=>[ // Same as default create
                    'extends'=>[':default:create']
                ],
                'delete'=>[ // Same as default create
                    'extends'=>[':default:create']
                ]
            ],
            'user'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>false, // users can't allowed to create
                    'permissive'=>false, // the following rules are defined here in order to be inherited further down
                    'fields'=>[
                        'users'=>[ // when this is inherited a user will be able to add them selves to an album
                            'permissive'=>false,
                            'enforce'=>[
                                'id'=>':userEntity:id' // When adding them selves to an album we enforce that the user is assigning their own user entity to the album
                            ],
                            'assign'=>[ // the can only add and remove them selves from an album
                                'add'=>true,
                                'remove'=>true,
                            ],
                            'chain'=>[
                                'read'=>true // Then can only add existing users, they can not create update or delete users in the process
                            ]
                        ]
                    ],
                ],
                'update'=>[
                    'extends'=>[':user:create'], // The same as create but it's allowed this time
                    'allowed'=>true,
                ],
                'delete'=>[
                    'extends'=>[':user:create']
                ],
            ],
            'admin'=>[ // Extends default because default has no additional rules on it, so super admins can do anything
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

}