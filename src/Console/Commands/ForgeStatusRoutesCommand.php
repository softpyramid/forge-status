<?php

namespace Softpyramid\ForgeStatus\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ForgeStatusRoutesCommand extends Command
{
    protected $signature = 'forge-status:routes';
    protected $description = 'List all Forge Status package routes';

    public function handle()
    {
        $this->info('Forge Status Package Routes:');
        $this->line('');

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return str_contains($route->getName() ?? '', 'forge-status');
        });

        if ($routes->isEmpty()) {
            $this->error('No Forge Status routes found!');
            $this->line('');
            $this->warn('Make sure the package is properly installed and the service provider is registered.');
            $this->line('Run: php artisan package:discover');
            return 1;
        }

        $headers = ['Method', 'URI', 'Name', 'Action'];
        $rows = [];

        foreach ($routes as $route) {
            $rows[] = [
                implode('|', $route->methods()),
                $route->uri(),
                $route->getName(),
                $route->getActionName(),
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info('✅ Forge Status routes are properly registered!');
        
        return 0;
    }
}
