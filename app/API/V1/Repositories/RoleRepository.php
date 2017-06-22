<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Role;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class RoleRepository extends Repository
{
	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = Role::class;
}