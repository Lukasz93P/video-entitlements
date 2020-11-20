<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;

class AssignCategoryToPlan extends ChangePlanCategory
{
    protected function changePlanCategory(BroadcastersPlans $broadcastersPlans, PlanId $planId, CategoryId $categoryId): void
    {
        $broadcastersPlans->assignCategoryToPlan($planId, $categoryId);
    }
}
