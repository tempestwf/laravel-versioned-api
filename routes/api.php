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

use App\API\V1\Controllers\AlbumController;

/** @var Dingo\Api\Routing\Router $api */
use TempestTools\AclMiddleware\Constants\PermissionsTemplatesConstants;
use TempestTools\Common\ArrayExpressions\ArrayExpressionBuilder;

$api = app('Dingo\Api\Routing\Router');



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
        'middleware' => ['basic.extractor', 'prime.controller'],
        'provider'   => 'V1',
        'permissions' => []
    ],
    function () use ($api)
    {
        $api->resource('album', AlbumController::class);
    }
);
