<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Integration\Application\Videos;

use Illuminate\Support\Facades\Event;
use Modules\Resources\Application\Videos\AddNewVideo;
use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Videos\NewVideoAdded;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddNewVideoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function videoDataProvider(): array
    {
        return [
            [BroadcasterId::generate(), 'some test title'],
            [BroadcasterId::generate(), 'some video title'],
            [BroadcasterId::generate(), '123 some different_test-title'],
        ];
    }

    /**
     * @dataProvider videoDataProvider
     */
    public function testShouldAddNewVideo(BroadcasterId $broadcasterId, string $title): void
    {
        dispatch_now(new AddNewVideo($broadcasterId->toString(), $title));

        Event::assertDispatched(
            NewVideoAdded::class,
            static fn (NewVideoAdded $event) => $event->broadcasterId() === $broadcasterId->toString()
                && $event->videoName() === $title
        );
    }
}
