<?php

namespace App\API\V3\Repositories;

use App\Repositories\Repository;

class OrganisationRepository extends Repository
{
	protected $entity = \App\API\V3\Entities\Organisation::class;
}