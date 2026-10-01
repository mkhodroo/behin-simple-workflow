<?php

namespace Behin\SimpleWorkflow;

use Behin\SimpleWorkflow\Console\Commands\ListElementsCommand;
use Behin\SimpleWorkflow\Console\Commands\MakeElementCommand;
use Behin\SimpleWorkflow\Elements\ElementRegistry;
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

        // المان‌های پیش‌فرض پکیج + المان‌هایی که اپ در config/workflow.php تعریف کرده است.
        $defaultElements = require __DIR__.'/config/elements.php';
        $this->app['config']->set('workflow.element_defaults', $defaultElements);

        // رجیستری المان‌ها: کلید singleton به نام workflow.elements و دسترسی از طریق app(ElementRegistry::class)
        $this->app->singleton('workflow.elements', function ($app) {
            $definitions = array_merge(
                $app['config']->get('workflow.element_defaults', []),
                $app['config']->get('workflow.elements', [])
            );

            return ElementRegistry::make($definitions);
        });
        $this->app->alias('workflow.elements', ElementRegistry::class);
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

        //دستورهای کنسولی مدیریت المان‌های فرایند
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeElementCommand::class,
                ListElementsCommand::class,
            ]);
        }
    }
}
