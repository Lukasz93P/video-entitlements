<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\BroadcastersPlans;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\BroadcastersPlans\RegisterBroadcaster;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\NewBroadcasterRegistered;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterBroadcasterTest extends TestCase
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
            [BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider broadcasterIdProvider
     */
    public function testShouldRegisterNewBroadcaster(BroadcasterId $broadcasterId): void
    {
        dispatch(new RegisterBroadcaster($broadcasterId->toString()));

        Event::assertDispatched(
            NewBroadcasterRegistered::class,
            static fn (NewBroadcasterRegistered $event) => $event->broadcasterId() === $broadcasterId->toString()
        );
    }
}
