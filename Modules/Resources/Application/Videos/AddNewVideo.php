<?php

declare(strict_types=1);

namespace Modules\Resources\Application\Videos;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Videos\Title;
use Modules\Resources\Domain\Videos\VideoFactory;
use Modules\Resources\Domain\Videos\VideoId;
use Modules\Resources\Domain\Videos\VideosRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class AddNewVideo extends ApplicationCommand
{
    private string $broadcasterId;

    private string $title;

    public function __construct(string $broadcasterId, string $title)
    {
        $this->broadcasterId = $broadcasterId;
        $this->title = $title;
    }

    /**
     * @throws DomainException
     */
    public function handle(VideosRepository $videosRepository): ExtractedEvents
    {
        $newVideo = VideoFactory::create(
            VideoId::generate(),
            BroadcasterId::fromString($this->broadcasterId),
            Title::fromString($this->title)
        );

        $extractedEvents = ExtractedEvents::extractEventsFrom($newVideo);

        $videosRepository->add($newVideo);

        return $extractedEvents;
    }
}
