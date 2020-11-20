<?php

declare(strict_types=1);

namespace Modules\Entitlements\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\Videos\EntitlementCheck;
use Modules\Entitlements\Domain\Videos\EntitlementCheck\SpecificationBasedEntitlementCheck;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\Entitlements\Infrastructure\BroadcastersPlans\BroadcastersPlansRedisRepository;
use Modules\Entitlements\Infrastructure\Videos\VideosRedisRepository;
use Modules\Entitlements\Infrastructure\Viewers\ViewersRedisRepository;

class EntitlementsServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Entitlements';

    protected string $moduleNameLower = 'entitlements';

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
        $this->app->bind(BroadcastersPlansRepository::class, BroadcastersPlansRedisRepository::class);
        $this->app->bind(VideosRepository::class, VideosRedisRepository::class);
        $this->app->bind(ViewersRepository::class, ViewersRedisRepository::class);
        $this->app->bind(EntitlementCheck::class, SpecificationBasedEntitlementCheck::class);
    }
}
