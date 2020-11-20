<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Application\Events;

use Modules\SharedKernel\Domain\Aggregate\Events\Event;

interface EventPublisher
{
    /**
     * @param Event[] $events
     */
    public function publish(array $events): void;
}
