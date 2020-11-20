<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\PlanGraph\PlanGraph;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;

final class PlanFactory
{
    private function __construct()
    {
    }

    public static function create(PlanId $id): Plan
    {
        return PlanGraph::create($id);
    }
}
