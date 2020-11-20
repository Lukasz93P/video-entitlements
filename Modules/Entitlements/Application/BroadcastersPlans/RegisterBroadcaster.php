<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansFactory;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterBroadcaster extends ApplicationCommand
{
    private string $broadcasterId;

    public function __construct(string $broadcasterId)
    {
        $this->broadcasterId = $broadcasterId;
    }

    /**
     * @throws DomainException
     */
    public function handle(BroadcastersPlansRepository $broadcastersPlansRepository): ExtractedEvents
    {
        $newBroadcasterPlans = BroadcastersPlansFactory::create(BroadcasterId::fromString($this->broadcasterId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($newBroadcasterPlans);

        $broadcastersPlansRepository->add($newBroadcasterPlans);

        return $extractedEvents;
    }
}
