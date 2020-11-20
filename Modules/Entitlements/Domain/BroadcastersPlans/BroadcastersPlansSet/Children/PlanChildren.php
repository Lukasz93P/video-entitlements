<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children;

use ArrayIterator;
use IteratorAggregate;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\DirectChildAlreadyAttached;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;

class PlanChildren implements Children, IteratorAggregate
{
    /**
     * @var Plan[]
     */
    private array $plans;

    private PlanCopier $planCopier;

    private function __construct(array $plans, PlanCopier $planCopier)
    {
        $this->plans = $plans;
        $this->planCopier = $planCopier;
    }

    public static function create(): Children
    {
        return new self([], PlanCopierFactory::create());
    }

    public function findChild(PlanId $childId): ?Plan
    {
        return $this->plans[$childId->toString()] ?? $this->searchAllDirectChildrenForIndirectChild($childId);
    }

    public function attachChildToParent(Plan $child, PlanId $parentId): void
    {
        $this->executeLogicOnPlanWhichIsOnPathToRequestedPlan(
            fn (Plan $plan) => $plan->associateChildWithParent($this->planCopier->copy($child), $parentId),
            $parentId
        );
    }

    public function detachDirectChild(PlanId $childId): void
    {
        unset($this->plans[$childId->toString()]);
    }

    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void
    {
        $this->executeLogicOnPlanWhichIsOnPathToRequestedPlan(
            fn (Plan $plan) => $plan->detachChildFromParent($childId, $parentId),
            $parentId
        );
    }

    public function attachDirectChild(Plan $child): void
    {
        if (isset($this->plans[$child->id()->toString()])) {
            throw DirectChildAlreadyAttached::create();
        }

        $this->attachPlan($child);
    }

    public function assignCategory(PlanId $planId, CategoryId $categoryId): void
    {
        $this->executeLogicOnPlanWhichIsOnPathToRequestedPlan(
            fn (Plan $plan) => $plan->assignCategory($planId, $categoryId),
            $planId
        );
    }

    public function unassignCategory(PlanId $planId, CategoryId $categoryId): void
    {
        $this->executeLogicOnPlanWhichIsOnPathToRequestedPlan(
            fn (Plan $plan) => $plan->unassignCategory($planId, $categoryId),
            $planId
        );
    }

    public function getIterator()
    {
        return new ArrayIterator($this->plans);
    }

    private function searchAllDirectChildrenForIndirectChild(PlanId $childId): ?Plan
    {
        foreach ($this->plans as $plan) {
            $foundChild = $plan->findChild($childId);
            if ($foundChild) {
                return $foundChild;
            }
        }

        return null;
    }

    private function isPlanOnPathToRequestedPlan(Plan $plan, PlanId $requestedPlanId): bool
    {
        return $plan->id()->equals($requestedPlanId) || $plan->findChild($requestedPlanId);
    }

    private function attachPlan(Plan $plan): void
    {
        $this->plans[$plan->id()->toString()] = $plan;
    }

    private function executeLogicOnPlanWhichIsOnPathToRequestedPlan(
        callable $logicToExecuteOnPlan,
        PlanId $requestedPlanId
    ): void {
        foreach ($this->plans as $plan) {
            if ($this->isPlanOnPathToRequestedPlan($plan, $requestedPlanId)) {
                $logicToExecuteOnPlan($plan);
            }
        }
    }
}
