<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class VideoUnassignedFromPlan extends Event
{
    private string $planId;

    public static function create(EventId $id, VideoId $videoId, PlanId $planId): self
    {
        $newInstance = self::raise($id, $videoId);

        $newInstance->planId = $planId->toString();

        return $newInstance;
    }

    public function videoId(): string
    {
        return $this->sourceId();
    }

    public function planId(): string
    {
        return $this->planId;
    }
}
