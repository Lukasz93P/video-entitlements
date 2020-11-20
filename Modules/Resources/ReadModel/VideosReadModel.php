<?php

declare(strict_types=1);

namespace Modules\Resources\ReadModel;

use Illuminate\Support\Facades\Redis;
use Modules\Resources\Domain\Videos\NewVideoAdded;

class VideosReadModel
{
    public const BROADCASTERS_VIDEOS_IDS_KEY_SPACE = 'resources_broadcasters_videos_ids';

    /**
     * @return string[]
     */
    public function getBroadcastersVideosIds(string $broadcasterId): array
    {
        $broadcastersVideosIds = Redis::get(self::BROADCASTERS_VIDEOS_IDS_KEY_SPACE.$broadcasterId);

        return json_decode($broadcastersVideosIds ?? '[]', true);
    }

    public function handle($videoEvent): void
    {
        if ($videoEvent instanceof NewVideoAdded) {
            $this->actualizeBroadcastersVideosIds($videoEvent);
        }
    }

    private function actualizeBroadcastersVideosIds(NewVideoAdded $newVideoAdded): void
    {
        $key = self::BROADCASTERS_VIDEOS_IDS_KEY_SPACE.$newVideoAdded->broadcasterId();

        $currentBroadcastersVideosIds = Redis::get($key);

        $actualizedBroadcastersVideosIds = array_merge(
            $currentBroadcastersVideosIds ? json_decode($currentBroadcastersVideosIds, true) : [],
            [$newVideoAdded->videoId()]
        );

        Redis::set($key, json_encode($actualizedBroadcastersVideosIds));
    }
}
