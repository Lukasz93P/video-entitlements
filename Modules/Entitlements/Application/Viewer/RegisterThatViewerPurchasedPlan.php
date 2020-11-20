<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Viewer;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterThatViewerPurchasedPlan extends ApplicationCommand
{
    private string $viewerId;

    private string $planId;

    private string $expiresAt;

    public function __construct(string $viewerId, string $planId, string $expiresAt)
    {
        $this->viewerId = $viewerId;
        $this->planId = $planId;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @throws DomainException
     */
    public function handle(ViewersRepository $viewersRepository): ExtractedEvents
    {
        $viewer = $viewersRepository->find(ViewerId::fromString($this->viewerId));

        $viewer->planPurchased(PlanId::fromString($this->planId), $this->expiresAt);

        $extractedEvents = ExtractedEvents::extractEventsFrom($viewer);

        $viewersRepository->add($viewer);

        return $extractedEvents;
    }
}
