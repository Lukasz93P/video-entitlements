<?php

declare(strict_types=1);

namespace Modules\Resources\Application\Plans;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Plans\Plan;
use Modules\Resources\Domain\Plans\PlanId;
use Modules\Resources\Domain\Plans\PlansRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class AddNewPlan extends ApplicationCommand
{
    private string $broadcasterId;

    public function __construct(string $broadcasterId)
    {
        $this->broadcasterId = $broadcasterId;
    }

    /**
     * @throws DomainException
     */
    public function handle(PlansRepository $plansRepository): ExtractedEvents
    {
        $newPlan = Plan::create(PlanId::generate(), BroadcasterId::fromString($this->broadcasterId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($newPlan);

        $plansRepository->add($newPlan);

        return $extractedEvents;
    }
}
