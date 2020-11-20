<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Application;

use Modules\SharedKernel\Application\Middlewares\EventsPublishingMiddleware;

abstract class ApplicationCommand
{
    final public function middleware(): array
    {
        return array_merge([EventsPublishingMiddleware::class], $this->extraMiddleware());
    }

    protected function extraMiddleware(): array
    {
        return [];
    }
}
