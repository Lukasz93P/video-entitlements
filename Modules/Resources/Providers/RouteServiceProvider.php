<?php

declare(strict_types=1);

namespace Modules\Resources\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\SharedKernel\Http\Requests\Validation;

class RouteServiceProvider extends ServiceProvider
{
    private const UUID_PATTERNS = [
        'broadcasterId' => Validation::UUID_REGEX_MATCH,
    ];

    protected string $moduleNamespace = 'Modules\Resources\Http\Controllers';

    public function boot(): void
    {
        foreach (self::UUID_PATTERNS as $parameterName => $pattern) {
            Route::pattern($parameterName, $pattern);
        }

        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Resources', '/Routes/api.php'))
        ;
    }
}
