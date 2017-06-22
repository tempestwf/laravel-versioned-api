<?php

namespace App\API\V1\Repositories;

use TempestTools\Crud\Doctrine\RepositoryAbstract;

class OrganisationRepository extends RepositoryAbstract
{
	protected $entity = \App\API\V1\Entities\Organisation::class;
}