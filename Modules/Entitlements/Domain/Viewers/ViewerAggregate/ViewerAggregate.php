<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers\ViewerAggregate;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosIdsCollection;
use Modules\Entitlements\Domain\Viewers\NewViewerRegistered;
use Modules\Entitlements\Domain\Viewers\Viewer;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewerPurchasedPerPerViewVideo;
use Modules\Entitlements\Domain\Viewers\ViewerPurchasedPlan;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class ViewerAggregate extends AggregateRoot implements Viewer
{
    private PurchasedPlans $purchasedPlans;

    private VideosIdsCollection $purchasedPayPerViewVideos;

    private function __construct(
        ViewerId $id,
        PurchasedPlans $purchasedPlans,
        VideosIdsCollection $purchasedPayPerViewVideos
    ) {
        parent::__construct($id);

        $this->purchasedPlans = $purchasedPlans;
        $this->purchasedPayPerViewVideos = $purchasedPayPerViewVideos;
    }

    public static function create(ViewerId $id): self
    {
        $newInstance = new self($id, PurchasedPlans::create(), VideosIdsCollection::create([]));

        $newInstance->registerRaisedEvent(NewViewerRegistered::create(EventId::generate(), $id));

        return $newInstance;
    }

    public function hasPurchasedAsPayPerView(VideoId $videoId): bool
    {
        return $this->purchasedPayPerViewVideos->contains($videoId);
    }

    public function purchasedActivePlansIds(): PlanIdsCollection
    {
        return PlanIdsCollection::create($this->purchasedPlans->activePlans());
    }

    public function planPurchased(PlanId $planId, string $expiresAt): void
    {
        $this->purchasedPlans->add($planId, $expiresAt);

        $this->registerRaisedEvent(ViewerPurchasedPlan::create(EventId::generate(), $this->id(), $planId, $expiresAt));
    }

    public function videoPayPerViewPurchased(VideoId $videoId): void
    {
        if ($this->hasPurchasedAsPayPerView($videoId)) {
            return;
        }

        $this->purchasedPayPerViewVideos->add($videoId);

        $this->registerRaisedEvent(ViewerPurchasedPerPerViewVideo::create(EventId::generate(), $this->id(), $videoId));
    }
}
