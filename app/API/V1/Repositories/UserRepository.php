<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use App\Repositories\Repository;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissions;
use TempestTools\AclMiddleware\Repository\HasPermissionsQueryTrait;

class UserRepository extends Repository implements RepoHasPermissions
{
    use HasPermissionsQueryTrait;
	protected $entity = User::class;
}