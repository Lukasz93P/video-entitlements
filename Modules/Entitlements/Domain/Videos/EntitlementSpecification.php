<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;

interface EntitlementSpecification
{
    public function meets(PlanIdsCollection $plansIds, CategoryIdsCollection $categoriesId): bool;
}
