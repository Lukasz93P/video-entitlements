<?php

namespace Modules\SharedKernel\Providers;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Modules\SharedKernel\Application\Events\EventPublisher;
use Modules\SharedKernel\Application\Middlewares\MiddlewareFiringDispatcher;
use Modules\SharedKernel\Infrastructure\Events\LaravelEventPublisher;

class SharedKernelServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'SharedKernel';

    protected string $moduleNameLower = 'sharedkernel';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->bindInterfacesImplementations();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerDecorators();
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

    private function registerDecorators(): void
    {
        $this->app->extend(
            Dispatcher::class,
            fn (Dispatcher $dispatcherToDecorate) => new MiddlewareFiringDispatcher($dispatcherToDecorate)
        );
    }

    private function bindInterfacesImplementations(): void
    {
        $this->app->bind(EventPublisher::class, LaravelEventPublisher::class);
    }
}
