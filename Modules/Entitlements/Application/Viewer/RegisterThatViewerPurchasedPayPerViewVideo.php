<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Viewer;

use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterThatViewerPurchasedPayPerViewVideo extends ApplicationCommand
{
    private string $viewerId;

    private string $videoId;

    public function __construct(string $viewerId, string $videoId)
    {
        $this->viewerId = $viewerId;
        $this->videoId = $videoId;
    }

    /**
     * @throws DomainException
     */
    public function handle(ViewersRepository $viewersRepository): ExtractedEvents
    {
        $viewer = $viewersRepository->find(ViewerId::fromString($this->viewerId));

        $viewer->videoPayPerViewPurchased(VideoId::fromString($this->videoId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($viewer);

        $viewersRepository->add($viewer);

        return $extractedEvents;
    }
}
