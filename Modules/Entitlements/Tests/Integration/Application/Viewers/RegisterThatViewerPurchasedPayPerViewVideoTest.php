<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Viewers;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Viewer\RegisterThatViewerPurchasedPayPerViewVideo;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewerPurchasedPerPerViewVideo;
use Modules\Entitlements\Tests\Fakes\FakesViewersRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterThatViewerPurchasedPayPerViewVideoTest extends TestCase
{
    use FakesViewersRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function viewerAndVideoIdsProvider(): array
    {
        return [
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
        ];
    }

    /**
     * @dataProvider viewerAndVideoIdsProvider
     */
    public function testShouldRegisterThatViewerPurchasedPayPerViewVideo(ViewerId $viewerId, VideoId $videoId): void
    {
        $this->fakeRepositoryContainsViewerWithId($viewerId);

        dispatch_now(new RegisterThatViewerPurchasedPayPerViewVideo($viewerId->toString(), $videoId->toString()));

        Event::assertDispatched(
            ViewerPurchasedPerPerViewVideo::class,
            fn (ViewerPurchasedPerPerViewVideo $event) => $event->viewerId() === $viewerId->toString()
                && $event->videoId() === $videoId->toString()
        );
    }
}
