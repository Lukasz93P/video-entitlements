<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterNewPlan extends ApplicationCommand
{
    private string $broadcasterId;

    private string $planId;

    public function __construct(string $broadcasterId, string $planId)
    {
        $this->broadcasterId = $broadcasterId;
        $this->planId = $planId;
    }

    /**
     * @throws DomainException
     */
    public function handle(BroadcastersPlansRepository $broadcastersPlansRepository): ExtractedEvents
    {
        $broadcastersPlans = $broadcastersPlansRepository->find(BroadcasterId::fromString($this->broadcasterId));

        $broadcastersPlans->registerNewPlan(PlanId::fromString($this->planId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($broadcastersPlans);

        $broadcastersPlansRepository->add($broadcastersPlans);

        return $extractedEvents;
    }
}
