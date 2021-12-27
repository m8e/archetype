<?php

namespace Archetype;

use App;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Archetype\Commands\DemoCommand;
use Archetype\Commands\ErrorsCommand;
use Archetype\Commands\DocumentationCommand;
use Archetype\Commands\ListAPICommand;
use Archetype\Commands\RelationshipsDemo;
use Archetype\Factories\LaravelFileFactory;
use Archetype\Factories\PHPFileFactory;
use Archetype\Traits\AddsLaravelStringsToStrWithMacros;

class ServiceProvider extends BaseServiceProvider
{
    use AddsLaravelStringsToStrWithMacros;

    public function register()
    {
        $this->registerFacades();
        $this->registerCommands();
        $this->mergeConfigFrom(__DIR__.'/config/archetype.php', 'archetype');
    }

    public function boot()
    {
        $this->bootStrMacros();
        $this->publishConfig();
    }

    protected function registerFacades()
    {
        App::bind('PHPFile', function () {
            return app()->make(PHPFileFactory::class);
        });

        App::bind('LaravelFile', function () {
            return app()->make(LaravelFileFactory::class);
        });
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/config/archetype.php' => config_path('archetype.php'),
        ]);
    }

    protected function registerCommands()
    {
        $this->commands([
            ListAPICommand::class,
            DemoCommand::class,
            RelationshipsDemo::class,
            ErrorsCommand::class,
            DocumentationCommand::class,
        ]);
    }
}
