<?php

namespace App\API\V1\Repositories;

use App\Repositories\Repository;

class OrganisationRepository extends Repository
{
	protected $entity = \App\API\V1\Entities\Organisation::class;
}