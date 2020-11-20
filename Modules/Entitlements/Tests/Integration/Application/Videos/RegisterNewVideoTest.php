<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Videos;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Videos\RegisterNewVideo;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\Videos\NewVideoRegistered;
use Modules\Entitlements\Domain\Videos\VideoId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterNewVideoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function videoDataProvider(): array
    {
        return [
            [VideoId::generate(), BroadcasterId::generate()],
            [VideoId::generate(), BroadcasterId::generate()],
            [VideoId::generate(), BroadcasterId::generate()],
            [VideoId::generate(), BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider videoDataProvider
     */
    public function testShouldRegisterNewVideo(VideoId $videoId, BroadcasterId $broadcasterId): void
    {
        dispatch_now(new RegisterNewVideo($videoId->toString(), $broadcasterId->toString()));

        Event::assertDispatched(
            NewVideoRegistered::class,
            fn (NewVideoRegistered $event) => $event->videoId() === $videoId->toString()
                && $event->broadcasterId() === $broadcasterId->toString()
        );
    }
}
