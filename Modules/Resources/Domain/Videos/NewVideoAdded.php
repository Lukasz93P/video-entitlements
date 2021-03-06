<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewVideoAdded extends VideoEvent
{
    public static function create(EventId $id, VideoId $videoId, BroadcasterId $broadcasterId, Title $title): self
    {
        return parent::create($id, $videoId, $broadcasterId, $title);
    }

    public function videoName(): string
    {
        return $this->title;
    }
}
