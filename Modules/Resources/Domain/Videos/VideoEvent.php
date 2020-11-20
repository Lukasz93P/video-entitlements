<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

abstract class VideoEvent extends Event
{
    protected string $title;

    private string $broadcasterId;

    public function videoId(): string
    {
        return $this->sourceId();
    }

    public function broadcasterId(): string
    {
        return $this->broadcasterId;
    }

    protected static function create(
        EventId $id,
        VideoId $videoId,
        BroadcasterId $broadcasterId,
        Title $title
    ): self {
        $newInstance = self::raise($id, $videoId);

        $newInstance->broadcasterId = $broadcasterId->toString();
        $newInstance->title = $title->toString();

        return $newInstance;
    }
}
