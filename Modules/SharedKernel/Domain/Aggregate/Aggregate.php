<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;

interface Aggregate
{
    public function id(): AggregateId;

    /**
     * @return Event[]
     */
    public function raisedEvents(): array;

    public function clearRaisedEvents(): void;
}
