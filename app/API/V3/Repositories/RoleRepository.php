<?php

namespace App\API\V3\Repositories;

use App\Repositories\Repository;

class RoleRepository extends Repository
{
	protected $entity = \App\API\V3\Entities\Role::class;
}