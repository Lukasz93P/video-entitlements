<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;

abstract class AggregateRoot implements Aggregate
{
    protected AggregateId $id;

    /**
     * @var Event[]
     */
    protected array $raisedEvents;

    /**
     * Root constructor.
     */
    protected function __construct(AggregateId $id)
    {
        $this->id = $id;
        $this->raisedEvents = [];
    }

    public function id(): AggregateId
    {
        return $this->id;
    }

    public function raisedEvents(): array
    {
        return $this->raisedEvents;
    }

    public function clearRaisedEvents(): void
    {
        $this->raisedEvents = [];
    }

    protected function registerRaisedEvent(Event $event): void
    {
        $this->raisedEvents[] = $event;
    }
}
