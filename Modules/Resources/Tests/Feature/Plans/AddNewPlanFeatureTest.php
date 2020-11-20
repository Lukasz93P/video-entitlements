<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Feature\Plans;

use Illuminate\Support\Facades\Event;
use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddNewPlanFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function broadcasterIdProvider(): array
    {
        return [
            [BroadcasterId::generate()],
            [BroadcasterId::generate()],
            [BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider broadcasterIdProvider
     */
    public function testShouldAcceptNewPlanCreateRequest(BroadcasterId $broadcasterId): void
    {
        $response = $this->postJson(
            route('broadcasters.plans.create', ['broadcasterId' => $broadcasterId->toString()])
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
