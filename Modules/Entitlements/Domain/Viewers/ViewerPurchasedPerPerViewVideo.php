<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class ViewerPurchasedPerPerViewVideo extends Event
{
    private string $videoId;

    public static function create(EventId $id, ViewerId $viewerId, VideoId $videoId): self
    {
        $newInstance = self::raise($id, $viewerId);

        $newInstance->videoId = $videoId->toString();

        return $newInstance;
    }

    public function videoId(): string
    {
        return $this->videoId;
    }

    public function viewerId(): string
    {
        return $this->sourceId();
    }
}
