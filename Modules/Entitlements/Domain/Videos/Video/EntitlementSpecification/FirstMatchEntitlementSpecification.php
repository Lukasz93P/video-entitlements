<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos\Video\EntitlementSpecification;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;

class FirstMatchEntitlementSpecification implements EntitlementSpecification
{
    private PlanIdsCollection $possiblePlansIds;

    private CategoryIdsCollection $possibleCategoriesIds;

    private function __construct(PlanIdsCollection $possiblePlansIds, CategoryIdsCollection $possibleCategoriesIds)
    {
        $this->possiblePlansIds = $possiblePlansIds;
        $this->possibleCategoriesIds = $possibleCategoriesIds;
    }

    public static function create(
        PlanIdsCollection $possiblePlansIds,
        CategoryIdsCollection $possibleCategoriesIds
    ): self {
        return new self($possiblePlansIds, $possibleCategoriesIds);
    }

    public function meets(PlanIdsCollection $planIds, CategoryIdsCollection $categoryIds): bool
    {
        if (!$this->possiblePlansIds->intersect($planIds)->isEmpty()) {
            return true;
        }

        if (!$this->possibleCategoriesIds->intersect($categoryIds)->isEmpty()) {
            return true;
        }

        return false;
    }
}
