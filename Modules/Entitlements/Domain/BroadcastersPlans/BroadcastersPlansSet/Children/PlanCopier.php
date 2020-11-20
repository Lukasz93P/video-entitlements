<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;

interface PlanCopier
{
    public function copy(Plan $plan): Plan;
}
