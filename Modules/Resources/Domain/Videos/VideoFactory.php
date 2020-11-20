<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Videos\VideoAggregate\VideoAggregate;

final class VideoFactory
{
    private function __construct()
    {
    }

    public static function create(VideoId $id, BroadcasterId $broadcasterId, Title $title): Video
    {
        return VideoAggregate::create($id, $broadcasterId, $title);
    }
}
