<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\Videos\VideoFactory;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterNewVideo extends ApplicationCommand
{
    private string $videoId;

    private string $broadcasterId;

    public function __construct(string $videoId, string $broadcasterId)
    {
        $this->videoId = $videoId;
        $this->broadcasterId = $broadcasterId;
    }

    /**
     * @throws DomainException
     */
    public function handle(VideosRepository $videosRepository): ExtractedEvents
    {
        $newVideo = VideoFactory::create(
            VideoId::fromString($this->videoId),
            BroadcasterId::fromString($this->broadcasterId)
        );

        $extractedEvents = ExtractedEvents::extractEventsFrom($newVideo);

        $videosRepository->add($newVideo);

        return $extractedEvents;
    }
}
