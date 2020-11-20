<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Integration\Application\Plans;

use Illuminate\Support\Facades\Event;
use Modules\Resources\Application\Plans\AddNewPlan;
use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Plans\NewPlanAdded;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AddNewPlanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function planDataProvider(): array
    {
        return [
            [BroadcasterId::generate()],
            [BroadcasterId::generate()],
            [BroadcasterId::generate()],
            [BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider planDataProvider
     */
    public function testShouldAddNewPlan(BroadcasterId $broadcasterId): void
    {
        dispatch_now(new AddNewPlan($broadcasterId->toString()));

        Event::assertDispatched(
            NewPlanAdded::class,
            static fn (NewPlanAdded $event) => $event->broadcasterId() === $broadcasterId->toString()
        );
    }
}
