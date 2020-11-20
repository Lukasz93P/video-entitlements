<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

interface Video extends Aggregate
{
    /**
     * @return VideoId
     */
    public function id(): AggregateId;

    public function assignToPlan(PlanId $planId): void;

    public function unassignFromPlan(PlanId $planId): void;

    public function assignToCategory(CategoryId $categoryId): void;

    public function unassignFromCategory(CategoryId $categoryId): void;

    public function broadcasterId(): BroadcasterId;

    public function createEntitlementSpecification(): EntitlementSpecification;
}
