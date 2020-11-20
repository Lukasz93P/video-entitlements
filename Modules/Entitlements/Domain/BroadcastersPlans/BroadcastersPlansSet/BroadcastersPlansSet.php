<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\Children;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\PlanChildren;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\PlanFactory;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeDetachedFromParent;
use Modules\Entitlements\Domain\BroadcastersPlans\NewBroadcasterRegistered;
use Modules\Entitlements\Domain\BroadcastersPlans\NewPlanRegistered;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanCannotBeRegistered;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanDetachedFromParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class BroadcastersPlansSet extends AggregateRoot implements BroadcastersPlans
{
    private Children $containedPlans;

    private function __construct(BroadcasterId $id, Children $containedPlans)
    {
        parent::__construct($id);

        $this->containedPlans = $containedPlans;
    }

    public static function create(BroadcasterId $id): self
    {
        $newInstance = new self($id, PlanChildren::create());

        $newInstance->registerRaisedEvent(NewBroadcasterRegistered::create(EventId::generate(), $id));

        return $newInstance;
    }

    public function registerNewPlan(PlanId $childId): void
    {
        if ($this->containedPlans->findChild($childId)) {
            throw PlanCannotBeRegistered::planAlreadyAdded($childId);
        }

        $this->containedPlans->attachDirectChild(PlanFactory::create($childId));

        $this->registerRaisedEvent(NewPlanRegistered::create(EventId::generate(), $this->id(), $childId));
    }

    public function attachChildToParent(PlanId $childId, PlanId $parentId): void
    {
        if (!$this->containedPlans->findChild($parentId)) {
            throw ChildCannotBeAttachedToParent::requestedParentDoesNotExists();
        }

        $childToAttach = $this->containedPlans->findChild($childId);

        if (!$childToAttach) {
            throw ChildCannotBeAttachedToParent::childNotFound();
        }

        $this->containedPlans->attachChildToParent($childToAttach, $parentId);

        $this->registerRaisedEvent(PlanAttachedToParent::create(EventId::generate(), $this->id(), $childId, $parentId));
    }

    public function detachChildFromParent(PlanId $childId, PlanId $parentId): void
    {
        $parent = $this->containedPlans->findChild($parentId);

        if (!$parent) {
            throw ChildCannotBeDetachedFromParent::requestedParentDoesNotExists();
        }

        if (!$parent->findChild($childId)) {
            throw ChildCannotBeDetachedFromParent::requestedParentCurrentDoesNotHaveRequestedChild();
        }

        $this->containedPlans->detachChildFromParent($childId, $parentId);

        $this->registerRaisedEvent(
            PlanDetachedFromParent::create(EventId::generate(), $this->id(), $childId, $parentId)
        );
    }

    public function doesPlanMeet(PlanId $planId, EntitlementSpecification $entitlementSpecification): bool
    {
        $planToCheckRequirementsFor = $this->containedPlans->findChild($planId);

        if (!$planToCheckRequirementsFor) {
            return false;
        }

        return $planToCheckRequirementsFor->meets($entitlementSpecification);
    }

    /**
     * This method has been created only for testing purposes and it's not a part of public contract.
     */
    public function findPlan(PlanId $planId): ?Plan
    {
        return $this->containedPlans->findChild($planId);
    }

    public function assignCategoryToPlan(PlanId $planId, CategoryId $categoryId): void
    {
        $this->containedPlans->assignCategory($planId, $categoryId);
    }

    public function unassignCategoryFromPlan(PlanId $planId, CategoryId $categoryId): void
    {
        $this->containedPlans->unassignCategory($planId, $categoryId);
    }
}
