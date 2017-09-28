<?php

namespace App\Http;

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TempestTools\AclMiddleware\Http\Middleware\Acl;
use TempestTools\Common\Laravel\Http\Middleware\BasicDataExtractor;

class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * These middleware are run during every request to your application.
	 *
	 * @var array
	 */
	protected $middleware = [
		CheckForMaintenanceMode::class,
	];
	
	/**
	 * The application's route middleware groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
			AddQueuedCookiesToResponse::class,
			StartSession::class,
			ShareErrorsFromSession::class,
		],
		
		'api' => [
			'throttle:60,1',
		],
	];
	
	/**
	 * The application's route middleware.
	 *
	 * These middleware may be assigned to groups or used individually.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth.basic' => AuthenticateWithBasicAuth::class,
		'throttle'   => ThrottleRequests::class,
        'acl' => Acl::class,
        'basic.extractor' => BasicDataExtractor::class
	];
}
