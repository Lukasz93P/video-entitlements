<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

abstract class ChangePlanCategory extends ApplicationCommand
{
    private string $broadcasterId;

    private string $planId;

    private string $categoryId;

    public function __construct(string $broadcasterId, string $planId, string $categoryId)
    {
        $this->broadcasterId = $broadcasterId;
        $this->planId = $planId;
        $this->categoryId = $categoryId;
    }

    /**
     * @throws DomainException
     */
    public function handle(BroadcastersPlansRepository $broadcastersPlansRepository): ExtractedEvents
    {
        $broadcastersPlans = $broadcastersPlansRepository->find(BroadcasterId::fromString($this->broadcasterId));

        $this->changePlanCategory(
            $broadcastersPlans,
            PlanId::fromString($this->planId),
            CategoryId::fromString($this->categoryId)
        );

        $extractedEvents = ExtractedEvents::extractEventsFrom($broadcastersPlans);

        $broadcastersPlansRepository->add($broadcastersPlans);

        return $extractedEvents;
    }

    /**
     * @throws DomainException
     */
    abstract protected function changePlanCategory(
        BroadcastersPlans $broadcastersPlans,
        PlanId $planId,
        CategoryId $categoryId
    ): void;
}
