<?php

declare(strict_types=1);

namespace Modules\Resources\Infrastructure\Videos;

use Illuminate\Support\Facades\Redis;
use Modules\Resources\Domain\Videos\Video;
use Modules\Resources\Domain\Videos\VideoId;
use Modules\Resources\Domain\Videos\VideosRepository;
use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

class VideosRedisRepository implements VideosRepository
{
    private const KEY_SPACE = 'resource_videos';

    public function add(Video $video): void
    {
        Redis::set($this->buildKeyForVideo($video->id()), serialize($video));
    }

    public function find(VideoId $id): Video
    {
        $result = Redis::get($this->buildKeyForVideo($id));

        if (!$result) {
            throw AggregateNotFound::create();
        }

        return unserialize($result, [Video::class]);
    }

    private function buildKeyForVideo(VideoId $id): string
    {
        return self::KEY_SPACE."_{$id->toString()}";
    }
}
