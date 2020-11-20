<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\DirectChildAlreadyAttached;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Traversable;

interface Children extends Traversable
{
    /**
     * @throws DirectChildAlreadyAttached
     */
    public function attachDirectChild(Plan $child);

    /**
     * @throws ChildCannotBeAttachedToParent
     */
    public function attachChildToParent(Plan $child, PlanId $parentId): void;

    public function detachDirectChild(PlanId $childId): void;

    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void;

    public function findChild(PlanId $childId): ?Plan;

    public function assignCategory(PlanId $planId, CategoryId $categoryId): void;

    public function unassignCategory(PlanId $planId, CategoryId $categoryId): void;
}
