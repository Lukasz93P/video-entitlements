<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Viewer;

use Modules\Entitlements\Domain\Viewers\ViewerFactory;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\SharedKernel\Application\ApplicationCommand;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Exceptions\DomainException;

class RegisterNewViewer extends ApplicationCommand
{
    private string $viewerId;

    public function __construct(string $viewerId)
    {
        $this->viewerId = $viewerId;
    }

    /**
     * @throws DomainException
     */
    public function handle(ViewersRepository $viewersRepository): ExtractedEvents
    {
        $newViewer = ViewerFactory::create(ViewerId::fromString($this->viewerId));

        $extractedEvents = ExtractedEvents::extractEventsFrom($newViewer);

        $viewersRepository->add($newViewer);

        return $extractedEvents;
    }
}
