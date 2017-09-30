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

use Dingo\Api\Routing\Router;
use App\API\V1\Controllers\AlbumController;

/** @var Dingo\Api\Routing\Router $api */

use App\API\V1\Controllers\ArtistController;
use App\API\V1\Controllers\UserController;
use TempestTools\AclMiddleware\Constants\PermissionsTemplatesConstants;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;

$api = app(Router::class);


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
        $api->resource('album', AlbumController::class);
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
        $api->resource('album/admin', AlbumController::class);
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
        $api->resource('artist', ArtistController::class);
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
        $api->resource('artist/admin', ArtistController::class);
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
        $api->resource('user', UserController::class);
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
        $api->resource('user/admin', UserController::class);
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
        $api->resource('user/superAdmin', UserController::class);
    }
);