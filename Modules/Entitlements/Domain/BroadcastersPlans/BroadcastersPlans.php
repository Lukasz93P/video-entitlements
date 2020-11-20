<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

interface BroadcastersPlans extends Aggregate
{
    /**
     * @return BroadcasterId
     */
    public function id(): AggregateId;

    /**
     * @throws PlanCannotBeRegistered
     */
    public function registerNewPlan(PlanId $childId): void;

    /**
     * @throws ChildCannotBeAttachedToParent
     */
    public function attachChildToParent(PlanId $childId, PlanId $parentId): void;

    /**
     * @throws ChildCannotBeDetachedFromParent
     */
    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void;

    public function assignCategoryToPlan(PlanId $planId, CategoryId $categoryId): void;

    public function unassignCategoryFromPlan(PlanId $planId, CategoryId $categoryId): void;

    public function doesPlanMeet(PlanId $planId, EntitlementSpecification $entitlementSpecification): bool;
}
