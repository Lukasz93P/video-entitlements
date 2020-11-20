<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate\Events;

use Carbon\Carbon;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

abstract class Event
{
    private EventId $id;

    private string $sourceId;

    private string $occurredAt;

    private function __construct(EventId $id, AggregateId $sourceId, string $occurredAt)
    {
        $this->id = $id;
        $this->sourceId = $sourceId->toString();
        $this->occurredAt = $occurredAt;
    }

    public function id(): string
    {
        return $this->id->toString();
    }

    public function occurredAt(): string
    {
        return $this->occurredAt;
    }

    protected static function raise(EventId $id, AggregateId $sourceId): self
    {
        return new static($id, $sourceId, Carbon::now()->toDateTimeString());
    }

    protected function sourceId(): string
    {
        return $this->sourceId;
    }
}
