<?php

namespace App\API\V1\Repositories;

use TempestTools\Crud\Doctrine\RepositoryAbstract;

class RoleRepository extends RepositoryAbstract
{
	protected $entity = \App\API\V1\Entities\Role::class;
}