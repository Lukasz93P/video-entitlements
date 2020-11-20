<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class AttachPlanToParent extends ChangePlanRelationship
{
    /**
     * @throws DomainException
     */
    public function handle(BroadcastersPlansRepository $broadcastersPlansRepository): ExtractedEvents
    {
        $broadcastersPlans = $broadcastersPlansRepository->find(BroadcasterId::fromString($this->broadcasterId));

        $broadcastersPlans->attachChildToParent(
            PlanId::fromString($this->childPlanId),
            PlanId::fromString($this->parentPlanId)
        );

        $extractedEvents = ExtractedEvents::extractEventsFrom($broadcastersPlans);

        $broadcastersPlansRepository->add($broadcastersPlans);

        return $extractedEvents;
    }
}
