<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BroadcasterRegistrationFeatureTest extends TestCase
{
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
    public function testShouldAcceptBroadcasterRegisterRequest(BroadcasterId $broadcasterId): void
    {
        $response = $this->postJson(route('broadcasters.create'), ['id' => $broadcasterId->toString()]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
