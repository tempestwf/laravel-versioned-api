<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $listen = [
    	'\TempestTools\Crud\Laravel\Doctrine\Events\Controller\Init'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PreIndex'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PostIndex'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PreStore'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PostStore'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PreShow'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PostShow'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PreUpdate'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PostUpdate'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PreDestroy'=>[],
        '\TempestTools\Crud\Laravel\Doctrine\Events\Controller\PostDestroy'=>[],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();

        //
    }
}
