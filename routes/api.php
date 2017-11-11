<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\API\V1\Controllers\ContextController;
use App\API\V1\Controllers\PermissionController;
use App\API\V1\Controllers\RoleController;
use Dingo\Api\Routing\Router;
use App\API\V1\Controllers\AlbumController;

/** @var Dingo\Api\Routing\Router $api */

use App\API\V1\Controllers\ArtistController;
use App\API\V1\Controllers\UserController;
use TempestTools\Moat\Constants\PermissionsTemplatesConstants;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;

$api = app(Router::class);

// Came with original skeleton

$api->version(
	'V1',
    [
		'provider'   => 'V1',
	],
	function () use ($api)
	{
		$api->post('auth/authenticate', 'App\API\V1\Controllers\AuthController@authenticate');
		$api->get('auth/refresh', 'App\API\V1\Controllers\AuthController@refresh');
	}
);

$api->version(
	'V1',
	[
		'middleware' => ['api.auth', 'basic.extractor', 'acl'],
		'provider'   => 'V1',
        'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI_AND_REQUEST_METHOD)]
	],
	function () use ($api)
	{
		$api->get('auth/me', 'App\API\V1\Controllers\UserController@me');
	}
);

// Scribe routes:

$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissions' => [],
        'ttPath'=>['guest'],
        'ttFallback'=>['default'],
        'configOverrides'=>[],
    ],
    function () use ($api)
    {
        $api->get('/contexts', ContextController::class . '@index');
        $api->get('/contexts/{context}', ContextController::class . '@show');
    }
);


$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
        'ttPath'=>['user'],
        'ttFallback'=>['default'],
        'configOverrides'=>[],
    ],
    function () use ($api)
    {
        $api->resources([
            '/contexts/user/users'=> UserController::class
        ]);
    }
);


$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
        'ttPath'=>['admin'],
        'ttFallback'=>['default'],
        'configOverrides'=>[],
    ],
    function () use ($api)
    {
        $api->resources([
            '/contexts/admin/users'=>UserController::class,
        ]);
    }
);

$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
        'ttPath'=>['superAdmin'],
        'ttFallback'=>['default'],
        'configOverrides'=>[],
    ],
    function () use ($api)
    {
        $api->resources([
            '/contexts/super-admin/users'=> UserController::class,
            '/contexts/super-admin/permissions'=>PermissionController::class,
            '/contexts/super-admin/roles'=>RoleController::class
        ]);
    }
);




