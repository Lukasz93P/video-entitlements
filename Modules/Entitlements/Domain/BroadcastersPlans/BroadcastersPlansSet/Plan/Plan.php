<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan;

use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

interface Plan extends Aggregate
{
    /**
     * @return PlanId
     */
    public function id(): AggregateId;

    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void;

    /**
     * @throws ChildCannotBeAttachedToParent
     */
    public function associateChildWithParent(Plan $child, PlanId $parentId): void;

    public function assignCategory(PlanId $planId, CategoryId $categoryId): void;

    public function unassignCategory(PlanId $planId, CategoryId $categoryId): void;

    public function findChild(PlanId $childId): ?Plan;

    public function meets(EntitlementSpecification $entitlementSpecification): bool;
}
