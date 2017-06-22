<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\Organisation;
use App\Repositories\Repository;

/** @noinspection LongInheritanceChainInspection */
class OrganisationRepository extends Repository
{
	protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */
        $entity = Organisation::class;
}