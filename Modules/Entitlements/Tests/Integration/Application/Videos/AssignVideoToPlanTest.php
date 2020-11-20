<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Videos;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Videos\AssignVideoToPlan;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Videos\VideoAssignedToPlan;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Tests\Fakes\FakesVideosRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AssignVideoToPlanTest extends TestCase
{
    use FakesVideosRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function videoAndPlanIdsProvider(): array
    {
        return [
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
        ];
    }

    /**
     * @dataProvider videoAndPlanIdsProvider
     */
    public function testShouldAssignVideoToPlan(VideoId $videoId, PlanId $planId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoId);

        dispatch_now(new AssignVideoToPlan($videoId->toString(), $planId->toString()));

        Event::assertDispatched(
            VideoAssignedToPlan::class,
            fn (VideoAssignedToPlan $event) => $event->planId() === $planId->toString()
                && $event->videoId() === $videoId->toString()
        );
    }
}
