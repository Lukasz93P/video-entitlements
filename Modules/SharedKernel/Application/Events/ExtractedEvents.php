<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Application\Events;

use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;

class ExtractedEvents
{
    /**
     * @var Event[]
     */
    private array $extractedEvents;

    /**
     * @param Event[]
     */
    private function __construct(array $events)
    {
        $this->extractedEvents = $events;
    }

    public static function extractEventsFrom(Aggregate ...$aggregates): self
    {
        $newInstance = new self([]);
        $newInstance->extractedEvents = $newInstance->extractEvents($aggregates);

        return $newInstance;
    }

    /**
     * @return Event[]
     */
    public function toArray(): array
    {
        return $this->extractedEvents;
    }

    /**
     * @param Aggregate[] $aggreagtes
     */
    private function extractEvents(array $aggreagtes): array
    {
        $extractedEvents = [];
        foreach ($aggreagtes as $aggregate) {
            $extractedEvents[] = $aggregate->raisedEvents();
            $aggregate->clearRaisedEvents();
        }

        return array_merge([], ...$extractedEvents);
    }
}
