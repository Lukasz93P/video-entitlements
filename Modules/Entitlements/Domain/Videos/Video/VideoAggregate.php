<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos\Video;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\Entitlements\Domain\Videos\NewVideoRegistered;
use Modules\Entitlements\Domain\Videos\Video;
use Modules\Entitlements\Domain\Videos\Video\EntitlementSpecification\FirstMatchEntitlementSpecification;
use Modules\Entitlements\Domain\Videos\VideoAssignedToPlan;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideoUnassignedFromPlan;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class VideoAggregate extends AggregateRoot implements Video
{
    private BroadcasterId $broadcasterId;

    private PlanIdsCollection $assignedPlansIds;

    private CategoryIdsCollection $assignedCategoriesIds;

    private function __construct(
        VideoId $id,
        BroadcasterId $broadcasterId,
        PlanIdsCollection $assignedPlansIds,
        CategoryIdsCollection $assignedCategoriesIds
    ) {
        parent::__construct($id);

        $this->broadcasterId = $broadcasterId;
        $this->assignedPlansIds = $assignedPlansIds;
        $this->assignedCategoriesIds = $assignedCategoriesIds;
    }

    public static function create(VideoId $id, BroadcasterId $broadcasterId): self
    {
        $newInstance = new self($id, $broadcasterId, PlanIdsCollection::create([]), CategoryIdsCollection::create([]));

        $newInstance->registerRaisedEvent(NewVideoRegistered::create(EventId::generate(), $id, $broadcasterId));

        return $newInstance;
    }

    public function assignedPlansIds(): PlanIdsCollection
    {
        return $this->assignedPlansIds;
    }

    public function assignToPlan(PlanId $planId): void
    {
        if ($this->isAssignedToPlan($planId)) {
            return;
        }

        $this->assignedPlansIds->add($planId);

        $this->registerRaisedEvent(VideoAssignedToPlan::create(EventId::generate(), $this->id(), $planId));
    }

    public function unassignFromPlan(PlanId $planId): void
    {
        if (!$this->isAssignedToPlan($planId)) {
            return;
        }

        $this->assignedPlansIds->remove($planId);

        $this->registerRaisedEvent(VideoUnassignedFromPlan::create(EventId::generate(), $this->id(), $planId));
    }

    public function broadcasterId(): BroadcasterId
    {
        return $this->broadcasterId;
    }

    public function assignToCategory(CategoryId $categoryId): void
    {
        if ($this->isAssignedToCategory($categoryId)) {
            return;
        }

        $this->assignedCategoriesIds->add($categoryId);
    }

    public function unassignFromCategory(CategoryId $categoryId): void
    {
        if (!$this->isAssignedToCategory($categoryId)) {
            return;
        }

        $this->assignedCategoriesIds->remove($categoryId);
    }

    public function assignedCategoriesIds(): CategoryIdsCollection
    {
        return $this->assignedCategoriesIds;
    }

    public function createEntitlementSpecification(): EntitlementSpecification
    {
        return FirstMatchEntitlementSpecification::create($this->assignedPlansIds(), $this->assignedCategoriesIds());
    }

    private function isAssignedToPlan(PlanId $planId): bool
    {
        return $this->assignedPlansIds->contains($planId);
    }

    private function isAssignedToCategory(CategoryId $categoryId): bool
    {
        return $this->assignedCategoriesIds->contains($categoryId);
    }
}
