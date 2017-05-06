<?php

namespace App\API\V3\Repositories;

use App\API\V3\Entities\User;
use App\Repositories\Repository;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissions;
use TempestTools\AclMiddleware\Repository\HasPermissionsQueryTrait;

class UserRepository extends Repository implements RepoHasPermissions
{
    use HasPermissionsQueryTrait;
	protected $entity = User::class;
}