<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Plans;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewPlanAdded extends Event
{
    private string $broadcasterId;

    public static function create(EventId $id, PlanId $planId, BroadcasterId $broadcasterId): self
    {
        $newInstance = self::raise($id, $planId);

        $newInstance->broadcasterId = $broadcasterId->toString();

        return $newInstance;
    }

    public function broadcasterId(): string
    {
        return $this->broadcasterId;
    }

    public function planId(): string
    {
        return $this->sourceId();
    }
}
