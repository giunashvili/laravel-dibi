<?php

namespace Cuonggt\Dibi;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;
use Exception;
use Illuminate\Support\ServiceProvider;

class DibiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dibi');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dibi.php' => config_path('dibi.php'),
            ], 'dibi-config');

            $this->publishes([
                __DIR__.'/../public' => public_path('vendor/dibi'),
            ], 'dibi-assets');

            $this->publishes([
                __DIR__.'/../stubs/DibiServiceProvider.stub' => app_path('Providers/DibiServiceProvider.php'),
            ], 'dibi-provider');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dibi.php', 'dibi'
        );

        $this->commands([
            Console\InstallCommand::class,
            Console\PublishCommand::class,
        ]);

        $this->registerServices();
    }

    /**
     * Register Dibi's services in the container.
     *
     * @return void
     */
    protected function registerServices()
    {
        $class = 'Cuonggt\\Dibi\\Repositories\\'.ucfirst(Dibi::driver()).'DatabaseRepository';

        if (! class_exists($class)) {
            throw new Exception('Database driver ['.Dibi::driver().'] is not supported.');
        }

        $this->app->bind(DatabaseRepository::class, $class);

        $this->app->when(MysqlDatabaseRepository::class)
            ->needs('$name')
            ->give(Dibi::databaseName());
    }
}
