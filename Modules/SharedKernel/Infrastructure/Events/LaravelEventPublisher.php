<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Infrastructure\Events;

use Modules\SharedKernel\Application\Events\EventPublisher;

class LaravelEventPublisher implements EventPublisher
{
    public function publish(array $events): void
    {
        foreach ($events as $event) {
            event($event);
        }
    }
}
