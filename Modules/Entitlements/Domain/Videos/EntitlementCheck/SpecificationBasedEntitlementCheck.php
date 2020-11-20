<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos\EntitlementCheck;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\Videos\EntitlementCheck;
use Modules\Entitlements\Domain\Videos\Video;
use Modules\Entitlements\Domain\Viewers\Viewer;

class SpecificationBasedEntitlementCheck implements EntitlementCheck
{
    private BroadcastersPlansRepository $broadcastersPlansRepository;

    public function __construct(BroadcastersPlansRepository $broadcastersPlansRepository)
    {
        $this->broadcastersPlansRepository = $broadcastersPlansRepository;
    }

    public function isEntitledToWatch(Video $video, Viewer $viewer): bool
    {
        if ($viewer->hasPurchasedAsPayPerView($video->id())) {
            return true;
        }

        return $this->isEntitledToWatchBasedOnPurchasedPlans($video, $viewer);
    }

    private function isEntitledToWatchBasedOnPurchasedPlans(Video $video, Viewer $viewer): bool
    {
        $entitlementSpecification = $video->createEntitlementSpecification();

        $videoBroadcasterPlans = $this->broadcastersPlansRepository->find($video->broadcasterId());

        foreach ($viewer->purchasedActivePlansIds() as $idOfPlanPurchasedByViewer) {
            $doesPlanPurchasedByViewerMeetsVideoEntitlementSpecification = $videoBroadcasterPlans
                ->doesPlanMeet($idOfPlanPurchasedByViewer, $entitlementSpecification)
            ;

            if ($doesPlanPurchasedByViewerMeetsVideoEntitlementSpecification) {
                return true;
            }
        }

        return false;
    }
}
