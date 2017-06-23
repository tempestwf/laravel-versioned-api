<?php
namespace App\API\V1\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use TempestTools\Crud\Laravel\EntityAbstract;

/** @noinspection LongInheritanceChainInspection */

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\ArtistRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_unq", columns={"name"})})
 */
class Artist extends EntityAbstract
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
     * @ORM\OneToMany(targetEntity="App\API\V1\Entities\Album", mappedBy="artist", cascade={"persist"})
     */
    private $albums;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->albums = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Artist
     */
    public function setId(int $id): Artist
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|NULL
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Artist
     */
    public function setName(string $name): Artist
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param Album $album
     * @return Artist
     */
    public function addAlbum(Album $album): Artist
    {
        $this->albums[] = $album;
        return $this;
    }

    /**
     * @param Album $album
     * @return Artist
     */
    public function removeAlbum(Album $album): Artist
    {
        $this->albums->removeElement($album);
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
                    'validator'=>[ // the only thing we enforce on artists is the validator
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
            'admin'=>[ // can do everything in default, and is allowed to do it when a super admin
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