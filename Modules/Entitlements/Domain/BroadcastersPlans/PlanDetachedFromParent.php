<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class PlanDetachedFromParent extends PlanRelationshipChanged
{
    public static function create(
        EventId $id,
        BroadcasterId $broadcasterId,
        PlanId $childPlanId,
        PlanId $parentPlanId
    ): self {
        return parent::create($id, $broadcasterId, $childPlanId, $parentPlanId);
    }
}
