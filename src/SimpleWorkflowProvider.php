<?php

namespace Behin\SimpleWorkflow;

use Behin\SimpleWorkflow\Middlewares\RedirectOldRoutes;
use Illuminate\Support\ServiceProvider;

class SimpleWorkflowProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/workflow.php', 'workflow');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__. '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadViewsFrom(__DIR__. '/Views', 'SimpleWorkflowView');
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'SimpleWorkflowLang');

        //ریدایرکت اینباکس قدیمی به جدید
        $this->app['router']->pushMiddlewareToGroup(
            'web',
            RedirectOldRoutes::class
        );
    }
}
