<?php

declare(strict_types=1);

namespace Modules\Entitlements\Integration\Listeners\Videos;

use Modules\Entitlements\Application\Videos\RegisterNewVideo;
use Modules\Resources\Domain\Videos\NewVideoAdded;

class VideoRegisteringListener
{
    public function handle(NewVideoAdded $newVideoAddedEvent): void
    {
        dispatch(new RegisterNewVideo($newVideoAddedEvent->videoId(), $newVideoAddedEvent->broadcasterId()));
    }
}
