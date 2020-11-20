<?php

declare(strict_types=1);

namespace Modules\Resources\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Resources\Domain\Plans\PlansRepository;
use Modules\Resources\Domain\Videos\VideosRepository;
use Modules\Resources\Infrastructure\Plans\PlansRedisRepository;
use Modules\Resources\Infrastructure\Videos\VideosRedisRepository;

class ResourcesServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Resources';

    protected string $moduleNameLower = 'resources';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->bindInterfacesImplementations();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->publishes(
            [
                module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    private function bindInterfacesImplementations(): void
    {
        $this->app->bind(VideosRepository::class, VideosRedisRepository::class);
        $this->app->bind(PlansRepository::class, PlansRedisRepository::class);
    }
}
