<?php

namespace App\API\V1\Repositories;

use App\Repositories\Repository;

class RoleRepository extends Repository
{
	protected $entity = \App\API\V1\Entities\Role::class;
}