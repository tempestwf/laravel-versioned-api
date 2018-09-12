<?php
namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';
	
	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->app->singleton(\Dingo\Api\Http\Validation\Domain::class, function ($app) {
            return new MultipleDomains([
                getenv('API_DOMAIN'),
                Request::server('HTTP_HOST'),
            ]);
        });

		parent::boot();
	}
	
	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->mapApiRoutes();
		//$this->mapWebRoutes();
	}
	
	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes()
	{
        /** @var \Dingo\Api\Routing\Router $api */
        $api = app('Dingo\Api\Routing\Router');

        $api->version('v1', [
            'middleware' => 'api',
            'domain' => Request::server('HTTP_HOST'),
        ], function ($api) {
            require base_path('routes/web.php');
        });
	}
	
	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes()
	{

        /** @var \Dingo\Api\Routing\Router $api */
        $api = app('Dingo\Api\Routing\Router');

        $api->version('v1', [
            'middleware' => 'api',
            'domain' => getenv('API_DOMAIN'),
        ], function ($api) {
            require base_path('routes/api.php');
        });

        $api->version('v1', [
            'middleware' => 'api',
            'domain' => Request::server('HTTP_HOST'),
        ], function ($api) {
            require base_path('routes/api.php');
        });
	}
}