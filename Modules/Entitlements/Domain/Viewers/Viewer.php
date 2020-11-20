<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

interface Viewer extends Aggregate
{
    /**
     * @return ViewerId
     */
    public function id(): AggregateId;

    public function hasPurchasedAsPayPerView(VideoId $videoId): bool;

    /**
     * @return PlanId[]|PlanIdsCollection
     */
    public function purchasedActivePlansIds(): PlanIdsCollection;

    public function planPurchased(PlanId $planId, string $expiresAt): void;

    public function videoPayPerViewPurchased(VideoId $videoId): void;
}
