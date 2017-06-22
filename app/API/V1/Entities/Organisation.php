<?php

namespace App\API\V1\Entities;

use Doctrine\ORM\Mapping AS ORM;
use LaravelDoctrine\ACL\Contracts\Organisation AS OrganisationContract;
use TempestTools\Crud\Laravel\EntityAbstract;

/**
 * @ORM\Entity(repositoryClass="App\API\V1\Repositories\OrganisationRepository")
 * @ORM\Table(name="organisations")
 */
class Organisation extends EntityAbstract implements OrganisationContract
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
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

    /**
     * @return array
     */
    public function getTTConfig(): array
    {
        return [
            'default'=>[
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
            'superAdmin'=>[
                'extends'=>[':default'],
                'allowed'=>true
            ]
        ];
    }
}