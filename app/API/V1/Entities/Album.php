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
     */
    public function addUser(User $user, bool $preventLoop = false)
    {
        if ($preventLoop === false) {
            $user->addAlbum($this, true);
        }

        $this->users[] = $user;
    }

    /**
     * @param User $user
     * @param bool $preventLoop
     */
    public function removeUser(User $user, bool $preventLoop = false)
    {
        if ($preventLoop === false) {
            $user->removeAlbum($this, true);
        }
        $this->users->removeElement($user);
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
     */
    public function setArtist(Artist $artist)
    {
        $this->artist = $artist;
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
     */
    public function setReleaseDate(string $releaseDate)
    {
        $this->releaseDate = $releaseDate;
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
     */
    public function setName(string $name)
    {
        $this->name = $name;
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
     */
    public function setId(int $id)
    {
        $this->id = $id;
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
                    'allowed'=>false,
                    'validator'=>[
                        'fields'=>[
                            'name',
                            'releaseDate'
                        ],
                        'rules'=>[
                            'name'=>'required|min:2',
                            'releaseDate'=>'required|date'
                        ],
                        'messages'=>NULL,
                        'customAttributes'=>NULL,
                    ],
                ],
                'update'=>[
                    'extends'=>':default:create'
                ],
                'delete'=>[
                    'extends'=>':default:create'
                ]
            ],
            'user'=>[
                'create'=>[
                    'extends'=>[':default:create'],
                    'allowed'=>true,
                    'permissive'=>false,
                    'fields'=>[
                        'users'=>[
                            'permissive'=>false,
                            'enforce'=>[
                                'id'=>':userEntity:id'
                            ],
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
                    'extends'=>':user:create'
                ],
                'delete'=>[
                    'extends'=>':user:create',
                    'allowed'=>false
                ],
            ],
            'superAdmin'=>[
                'create'=>[
                    'extends'=>':default:create',
                    'allowed'=>true
                ],
                'update'=>[
                    'extends'=>':default:create',
                    'allowed'=>true
                ],
                'delete'=>[
                    'extends'=>':default:create',
                    'allowed'=>true
                ],
            ]
        ];
    }

}