<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Videos;

use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\Video;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

abstract class ChangeVideoCategory extends ApplicationCommand
{
    private string $videoId;

    private string $categoryId;

    public function __construct(string $videoId, string $categoryId)
    {
        $this->videoId = $videoId;
        $this->categoryId = $categoryId;
    }

    /**
     * @throws DomainException
     */
    public function handle(VideosRepository $videosRepository): ExtractedEvents
    {
        $video = $videosRepository->find(VideoId::fromString($this->videoId));

        $this->changeVideoCategory($video, CategoryId::fromString($this->categoryId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($video);

        $videosRepository->add($video);

        return $extractedEvents;
    }

    /**
     * @throws DomainException
     */
    abstract protected function changeVideoCategory(Video $video, CategoryId $categoryId): void;
}
