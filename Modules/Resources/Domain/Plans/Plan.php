<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Plans;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class Plan extends AggregateRoot
{
    private BroadcasterId $broadcasterId;

    private function __construct(PlanId $id, BroadcasterId $broadcasterId)
    {
        parent::__construct($id);

        $this->broadcasterId = $broadcasterId;
    }

    public static function create(PlanId $id, BroadcasterId $broadcasterId): self
    {
        $newInstance = new self($id, $broadcasterId);

        $newInstance->registerRaisedEvent(NewPlanAdded::create(EventId::generate(), $id, $broadcasterId));

        return $newInstance;
    }

    public function broadcasterId(): BroadcasterId
    {
        return $this->broadcasterId;
    }
}
