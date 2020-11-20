<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate\Events;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\UuidBasedAggregateId;

class EventId extends UuidBasedAggregateId
{
}
