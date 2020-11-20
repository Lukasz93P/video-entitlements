<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

interface VideosRepository
{
    public function add(Video $video): void;

    /**
     * @throws AggregateNotFound
     */
    public function find(VideoId $videoId): Video;
}
