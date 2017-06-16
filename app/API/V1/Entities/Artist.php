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
     * @ORM\OneToMany(targetEntity="App\API\V1\Entities\Album", mappedBy="artist", cascade={"persist","remove"})
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function addAlbum(Album $album)
    {
        $this->albums[] = $album;
    }

    /**
     * @param Album $album
     */
    public function removeAlbum(Album $album)
    {
        $this->albums->removeElement($album);
    }
}