<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Videos;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class AssignVideoToPlan extends ApplicationCommand
{
    private string $videoId;

    private string $planId;

    public function __construct(string $videoId, string $planId)
    {
        $this->videoId = $videoId;
        $this->planId = $planId;
    }

    /**
     * @throws DomainException
     */
    public function handle(VideosRepository $videosRepository): ExtractedEvents
    {
        $video = $videosRepository->find(VideoId::fromString($this->videoId));

        $video->assignToPlan(PlanId::fromString($this->planId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($video);

        $videosRepository->add($video);

        return $extractedEvents;
    }
}
