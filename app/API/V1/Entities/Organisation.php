<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping AS ORM;
use LaravelDoctrine\ACL\Contracts\Organisation AS OrganisationContract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\OrganisationRepository")
 * @ORM\Table(name="organisations")
 */
class Organisation implements OrganisationContract
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

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
     * @param string $name
     * @return Organisation
     */
    public function setName(string $name): Organisation
    {
        $this->name = $name;
        return $this;
    }
}