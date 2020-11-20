<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Videos;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Videos\AssignVideoToPlan;
use Modules\Entitlements\Application\Videos\UnassignVideoFromPlan;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideoUnassignedFromPlan;
use Modules\Entitlements\Tests\Fakes\FakesVideosRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UnassignVideoFromPlanTest extends TestCase
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
            [VideoId::generate(), PlanId::generate()],
        ];
    }

    /**
     * @dataProvider videoAndPlanIdsProvider
     */
    public function testShouldUnassignVideoFromPlan(VideoId $videoId, PlanId $planId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoId);

        dispatch_now(new AssignVideoToPlan($videoId->toString(), $planId->toString()));

        dispatch_now(new UnassignVideoFromPlan($videoId->toString(), $planId->toString()));

        Event::assertDispatched(
            VideoUnassignedFromPlan::class,
            fn (VideoUnassignedFromPlan $event) => $event->planId() === $planId->toString()
                && $event->videoId() === $videoId->toString()
        );
    }
}
