<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

abstract class PlanRelationshipChanged extends Event
{
    private string $childPlanId;

    private string $parentPlanId;

    public function broadcasterId(): string
    {
        return $this->sourceId();
    }

    public function childPlanId(): string
    {
        return $this->childPlanId;
    }

    public function parentPlanId(): string
    {
        return $this->parentPlanId;
    }

    protected static function create(
        EventId $id,
        BroadcasterId $broadcasterId,
        PlanId $childPlanId,
        PlanId $parentPlanId
    ): self {
        $newInstance = self::raise($id, $broadcasterId);

        $newInstance->childPlanId = $childPlanId->toString();
        $newInstance->parentPlanId = $parentPlanId->toString();

        return $newInstance;
    }
}
