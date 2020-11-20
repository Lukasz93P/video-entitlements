<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\PlanGraph;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\Children;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\PlanChildren;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\DirectChildAlreadyAttached;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;

class PlanGraph extends AggregateRoot implements Plan
{
    /**
     * @var Children|Plan[]
     */
    private Children $children;

    private CategoryIdsCollection $assignedCategoriesIds;

    private function __construct(PlanId $id, Children $containedPlans, CategoryIdsCollection $assignedCategoriesIds)
    {
        parent::__construct($id);

        $this->children = $containedPlans;
        $this->assignedCategoriesIds = $assignedCategoriesIds;
    }

    public static function create(PlanId $id): self
    {
        return new self($id, PlanChildren::create(), CategoryIdsCollection::create([]));
    }

    public function associateChildWithParent(Plan $child, PlanId $parentId): void
    {
        if ($parentId->equals($this->id())) {
            $this->addDirectChild($child);
        } else {
            $this->children->attachChildToParent($child, $parentId);
        }
    }

    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void
    {
        if ($parentId->equals($this->id())) {
            $this->children->detachDirectChild($childId);
        } else {
            $this->children->detachChildFromParent($childId, $parentId);
        }
    }

    public function findChild(PlanId $childId): ?Plan
    {
        return $this->children->findChild($childId);
    }

    public function assignCategory(PlanId $planId, CategoryId $categoryId): void
    {
        if ($planId->equals($this->id())) {
            $this->assignedCategoriesIds->add($categoryId);
        } else {
            $this->children->assignCategory($planId, $categoryId);
        }
    }

    public function unassignCategory(PlanId $planId, CategoryId $categoryId): void
    {
        if ($planId->equals($this->id())) {
            $this->assignedCategoriesIds->remove($categoryId);
        } else {
            $this->children->unassignCategory($planId, $categoryId);
        }
    }

    public function assignedCategoriesIds(): CategoryIdsCollection
    {
        return $this->assignedCategoriesIds;
    }

    public function meets(EntitlementSpecification $entitlementSpecification): bool
    {
        if ($this->meetsSpecificationByItself($entitlementSpecification)) {
            return true;
        }

        foreach ($this->children as $childPlan) {
            if ($childPlan->meets($entitlementSpecification)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ChildCannotBeAttachedToParent
     */
    private function addDirectChild(Plan $child): void
    {
        $this->validateParentRelation($child);

        try {
            $this->children->attachDirectChild($child);
        } catch (DirectChildAlreadyAttached $childAlreadyAttached) {
            throw ChildCannotBeAttachedToParent::planIsAlreadyDirectChildOfRequestedParent();
        }
    }

    /**
     * @throws ChildCannotBeAttachedToParent
     */
    private function validateParentRelation(Plan $potentialChild): void
    {
        if ($this->equals($potentialChild)) {
            throw ChildCannotBeAttachedToParent::planCannotBeChildOfItself();
        }
        if ($potentialChild->findChild($this->id())) {
            throw ChildCannotBeAttachedToParent::currentParentCannotBeAddedAsChild();
        }
    }

    private function equals(Plan $plan): bool
    {
        return $this->id()->equals($plan->id());
    }

    private function meetsSpecificationByItself(EntitlementSpecification $entitlementSpecification): bool
    {
        return $entitlementSpecification->meets(PlanIdsCollection::create([$this->id()]), $this->assignedCategoriesIds);
    }
}
