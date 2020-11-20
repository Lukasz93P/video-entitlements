<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewBroadcasterRegistered extends Event
{
    public static function create(EventId $id, BroadcasterId $broadcasterId): self
    {
        return self::raise($id, $broadcasterId);
    }

    public function broadcasterId(): string
    {
        return $this->sourceId();
    }
}
