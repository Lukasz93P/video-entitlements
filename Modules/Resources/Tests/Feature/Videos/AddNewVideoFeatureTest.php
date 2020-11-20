<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Feature\Videos;

use Illuminate\Support\Facades\Event;
use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddNewVideoFeatureTest extends TestCase
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
    public function testShouldAcceptNewVideoCreateRequest(BroadcasterId $broadcasterId, string $title): void
    {
        $response = $this->postJson(
            route('broadcasters.videos.create', ['broadcasterId' => $broadcasterId->toString()]),
            ['title' => $title]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
