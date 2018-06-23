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

use App\API\V1\Controllers\EmailVerificationController;
use App\API\V1\Controllers\IndexController;
use App\API\V1\Controllers\AuthController;
use App\API\V1\Controllers\ContextController;
use App\API\V1\Controllers\PasswordResetController;
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

// For testing:
$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
        'additionalExtractors' =>[ArrayExpressionBuilder::closure(function ($params) {
            /** @var UserController $controller */
            $controller = $params['controller'];
            return $controller->getUser();
        })]
    ],
    function () use ($api)
    {
        $api->resources([
            'fail/user'=> UserController::class
        ]);
    }
);

$api->version(
    'V1',
    [
        'middleware' => ['basic.extractor', 'prime.controller', 'acl'],
        'provider'   => 'V1',
        'permissionClosures' =>[ArrayExpressionBuilder::closure(function () {
            return false;
        })]
    ],
    function () use ($api)
    {
        $api->resources([
            'fail2/user'=> UserController::class
        ]);
    }
);

// Came with original skeleton

$api->version(
    'V1',
    [
        'provider'   => 'V1',
    ],
    function () use ($api)
    {
        $api->get('/', IndexController::class . '@healthCheck');
    }
);

$api->group(
    [
        'version'   => 'V1',
        'provider'   => 'V1',
        'domain'     => '{subDomain}.{domainName}.{topLevelDomain}'
    ],
    function () use ($api) {
        $api->version(
            'V1',
            [
                'provider'   => 'V1'
            ],
            function () use ($api)
            {
                $api->post('auth/authenticate', AuthController::class . '@authenticate');
                $api->get('auth/refresh',  AuthController::class . '@refresh');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', 'raven', 'recaptcha'],
                'provider'   => 'V1',
                'ttPath'=>['guest'],
                'ttFallback'=>['default'],
            ],
            function () use ($api)
            {
                $api->post('/contexts/guest/users', UserController::class . '@store');
            }
        );

        $api->version(
            'V1',
            [
                //'middleware' => ['prime.controller'],
                'provider'   => 'V1',
                //'ttPath'=>['guest'],
                //'ttFallback'=>['default'],
            ],
            function () use ($api)
            {
                //$api->get('/activate/{code}', UserController::class . '@activate');
                $api->get('/auth/authenticate/{provider}', AuthController::class . '@getSocialAuth');
                $api->get('/auth/authenticate/callback/{provider}', AuthController::class . '@getSocialAuthCallback');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['jwt.auth', 'api.auth', 'acl', 'localization'],
                'provider'   => 'V1',
                //'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI_AND_REQUEST_METHOD)],
            ],
            function () use ($api)
            {
                $api->get('auth/me', UserController::class . '@me');
            }
        );

        // Scribe routes:

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', /*'acl',*/ 'localization', 'raven'],
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
                $api->get('/contexts/guest/albums', AlbumController::class . '@index');
                $api->get('/contexts/guest/artists', ArtistController::class . '@index');
                $api->get('/contexts/guest/albums/{id}', AlbumController::class . '@show');
                $api->get('/contexts/guest/artists/{id}', ArtistController::class . '@show');
                $api->get('/contexts/guest/email-verification/{id}', EmailVerificationController::class . '@show');
                $api->put('/contexts/guest/email-verification/{id}', EmailVerificationController::class . '@update');
                $api->get('/contexts/guest/password-reset/{id}', PasswordResetController::class . '@show');
                $api->put('/contexts/guest/password-reset/{id}', PasswordResetController::class . '@update');
                $api->post('/contexts/guest/password-reset', PasswordResetController::class . '@store');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['jwt.auth', 'basic.extractor', 'prime.controller', 'acl', 'localization'],
                'provider'   => 'V1',
                'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
                'ttPath'=>['user'],
                'ttFallback'=>['default'],
                'configOverrides'=>[],
            ],
            function () use ($api)
            {
                $api->resources([
                    '/contexts/user/albums'=> AlbumController::class,
                    '/contexts/user/artists'=>ArtistController::class,
                    '/contexts/user/users'=> UserController::class
                ]);
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', 'acl', 'localization'],
                'provider'   => 'V1',
                // For reasons I can not figure out dingo with laravel doesn't always put a slash at the front of a uri internally. It's unreliable so I made this template to compensate
                'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI_FORCE_FIRST_SLASH)],
                'ttPath'=>['user/users'],
                'ttFallback'=>['default'],
                'configOverrides'=>[],
            ],
            function () use ($api)
            {
                $api->get('/contexts/user/users/{user}/albums', AlbumController::class . '@index');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', 'acl', 'localization'],
                'provider'   => 'V1',
                'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI_FORCE_FIRST_SLASH)],
                'ttPath'=>['admin/users'],
                'ttFallback'=>['default'],
                'configOverrides'=>[],
            ],
            function () use ($api)
            {
                $api->get('/contexts/admin/users/{user}/albums', AlbumController::class . '@index');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', 'acl', 'localization'],
                'provider'   => 'V1',
                'permissions' => [],
                'ttPath'=>['guest/artists'],
                'ttFallback'=>['default'],
                'configOverrides'=>[],
            ],
            function () use ($api)
            {
                $api->get('/contexts/guest/artists/{artist}/albums', AlbumController::class . '@index');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['jwt.auth', 'api.auth', 'basic.extractor', 'prime.controller', 'acl', 'localization', 'raven'],
                'provider'   => 'V1',
                'permissions' => [ArrayExpressionBuilder::template(PermissionsTemplatesConstants::URI)],
                'ttPath'=>['admin'],
                'ttFallback'=>['default'],
                'configOverrides'=>[],
            ],
            function () use ($api)
            {
                $api->resources([
                    '/contexts/admin/albums'=>AlbumController::class,
                    '/contexts/admin/artists'=>ArtistController::class,
                    '/contexts/admin/users'=>UserController::class,
                    '/contexts/admin/email-verification'=>EmailVerificationController::class,
                ]);
                $api->get('/contexts/admin/email-verification/{id}', EmailVerificationController::class . '@show');
            }
        );

        $api->version(
            'V1',
            [
                'middleware' => ['basic.extractor', 'prime.controller', 'acl', 'localization'],
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
    }
);