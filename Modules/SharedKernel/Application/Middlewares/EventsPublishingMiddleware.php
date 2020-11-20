<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Application\Middlewares;

use Modules\SharedKernel\Application\Events\EventPublisher;
use Modules\SharedKernel\Application\Events\ExtractedEvents;

class EventsPublishingMiddleware
{
    private EventPublisher $eventsPublisher;

    public function __construct(EventPublisher $eventsPublisher)
    {
        $this->eventsPublisher = $eventsPublisher;
    }

    public function handle($job, callable $next)
    {
        $valueReturnedByNextClosure = $next($job);
        if ($valueReturnedByNextClosure instanceof ExtractedEvents) {
            $this->eventsPublisher->publish($valueReturnedByNextClosure->toArray());
        }

        return $valueReturnedByNextClosure;
    }
}
