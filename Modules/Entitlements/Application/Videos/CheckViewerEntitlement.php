<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Videos;

use Modules\Entitlements\Domain\Videos\EntitlementCheck;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class CheckViewerEntitlement
{
    private EntitlementCheck $entitlementCheck;

    private ViewersRepository $viewersRepository;

    private VideosRepository $videosRepository;

    public function __construct(
        EntitlementCheck $entitlementCheck,
        ViewersRepository $viewersRepository,
        VideosRepository $videosRepository
    ) {
        $this->entitlementCheck = $entitlementCheck;
        $this->viewersRepository = $viewersRepository;
        $this->videosRepository = $videosRepository;
    }

    /**
     * @throws DomainException
     */
    public function isViewerEntitledToWatchWideo(string $viewerId, string $videoId): bool
    {
        $viewer = $this->viewersRepository->find(ViewerId::fromString($viewerId));
        $video = $this->videosRepository->find(VideoId::fromString($videoId));

        return $this->entitlementCheck->isEntitledToWatch($video, $viewer);
    }
}
