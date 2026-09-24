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
        require_once __DIR__ . '/Helper/behin-simple-workflow.php';
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
        //گزارش کارتابل تسک‌ها (کاملا مجزا از سایر روت‌ها، کنترلرها و ویوها)
        $this->loadRoutesFrom(__DIR__ . '/Report/Routes/report.php');
        $this->loadViewsFrom(__DIR__. '/Views', 'SimpleWorkflowView');
        //ویوهای گزارش کارتابل تسک‌ها (مجزا)
        $this->loadViewsFrom(__DIR__ . '/Report/Views', 'SimpleWorkflowReportView');
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'SimpleWorkflowLang');

        //ریدایرکت اینباکس قدیمی به جدید
        $this->app['router']->pushMiddlewareToGroup(
            'web',
            RedirectOldRoutes::class
        );
    }
}
