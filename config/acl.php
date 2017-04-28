<?php

use App\API\V3\Entities\Role;
use App\API\V3\Entities\Organisation;

return [

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    'roles'         => [
        'entity' => Role::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Available drivers: config|doctrine
    | When set to config, add the permission names to list
    |
    */
    'permissions'   => [
        'driver' => 'config',
        'entity' => LaravelDoctrine\ACL\Permissions\Permission::class,
        'list'   => [
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Organisations
    |--------------------------------------------------------------------------
    */
    'organisations' => [
        'entity' => Organisation::class,
    ],
];
