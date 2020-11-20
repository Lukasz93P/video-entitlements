<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\Videos\Video\VideoAggregate;

final class VideoFactory
{
    private function __construct()
    {
    }

    public static function create(VideoId $id, BroadcasterId $broadcasterId): Video
    {
        return VideoAggregate::create($id, $broadcasterId);
    }
}
