<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Viewers;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Viewer\RegisterNewViewer;
use Modules\Entitlements\Domain\Viewers\NewViewerRegistered;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterNewViewerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function viewerIdProvider(): array
    {
        return [
            [ViewerId::generate()],
            [ViewerId::generate()],
            [ViewerId::generate()],
        ];
    }

    /**
     * @dataProvider viewerIdProvider
     */
    public function testShouldRegisterNewViewer(ViewerId $viewerId): void
    {
        dispatch_now(new RegisterNewViewer($viewerId->toString()));

        Event::assertDispatched(
            NewViewerRegistered::class,
            fn (NewViewerRegistered $event) => $event->viewerId() === $viewerId->toString()
        );
    }
}
