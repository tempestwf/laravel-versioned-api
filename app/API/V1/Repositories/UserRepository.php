<?php

namespace App\API\V1\Repositories;

use App\API\V1\Entities\User;
use TempestTools\AclMiddleware\Contracts\RepoHasPermissions;
use TempestTools\AclMiddleware\Repository\HasPermissionsQueryTrait;
use TempestTools\Crud\Doctrine\RepositoryAbstract;

class UserRepository extends RepositoryAbstract implements RepoHasPermissions
{
    use HasPermissionsQueryTrait;
	protected $entity = User::class;
}