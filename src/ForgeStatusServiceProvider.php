<?php

namespace Softpyramid\ForgeStatus;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Softpyramid\ForgeStatus\View\Components\DeploymentIndicator;
use Softpyramid\ForgeStatus\Console\Commands\ForgeStatusRoutesCommand;

class ForgeStatusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../resources/config/forge-status.php',
            'forge-status'
        );
    }

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'forge-status');
        
        // Register Blade component
        Blade::component('forge-deployment-indicator', DeploymentIndicator::class);
        
        // Publish config
        $this->publishes([
            __DIR__.'/../resources/config/forge-status.php' => config_path('forge-status.php'),
        ], 'forge-status-config');
        
        // Register console commands for debugging
        if ($this->app->runningInConsole()) {
            $this->commands([
                ForgeStatusRoutesCommand::class,
            ]);
        }
    }
}
