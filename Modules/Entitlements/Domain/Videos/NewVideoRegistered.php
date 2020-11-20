<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewVideoRegistered extends Event
{
    private string $broadcasterId;

    public static function create(EventId $id, VideoId $videoId, BroadcasterId $broadcasterId): self
    {
        $newInstance = self::raise($id, $videoId);

        $newInstance->broadcasterId = $broadcasterId->toString();

        return $newInstance;
    }

    public function videoId(): string
    {
        return $this->sourceId();
    }

    public function broadcasterId(): string
    {
        return $this->broadcasterId;
    }
}
