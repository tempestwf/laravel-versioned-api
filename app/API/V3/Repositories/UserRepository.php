<?php

namespace App\API\V3\Repositories;

use App\Repositories\Repository;
use TempestTools\AclMiddleware\Permissions\HasPermissionsQueryTrait;

class UserRepository extends Repository
{
    use HasPermissionsQueryTrait;
	protected $entity = \App\API\V3\Entities\User::class;
}