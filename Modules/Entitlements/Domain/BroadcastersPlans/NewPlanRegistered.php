<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewPlanRegistered extends Event
{
    private string $registeredPlanId;

    public static function create(EventId $id, BroadcasterId $broadcasterId, PlanId $registeredPlanId): self
    {
        $newInstance = self::raise($id, $broadcasterId);

        $newInstance->registeredPlanId = $registeredPlanId->toString();

        return $newInstance;
    }

    public function broadcasterId(): string
    {
        return $this->sourceId();
    }

    public function registeredPlanId(): string
    {
        return $this->registeredPlanId;
    }
}
