<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Tests\Fakes\FakesVideosRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VideoPlanAssignmentFeatureTest extends TestCase
{
    use FakesVideosRepository;

    public function videoAndPlanIdProvider(): array
    {
        return [
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
            [VideoId::generate(), PlanId::generate()],
        ];
    }

    /**
     * @dataProvider videoAndPlanIdProvider
     */
    public function testShouldAcceptVideoAssigningToPlanRequest(VideoId $videoIdId, PlanId $planId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoIdId);

        $response = $this->put(
            route('videos.plans.assign', ['videoId' => $videoIdId->toString()]),
            ['planId' => $planId->toString()]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @dataProvider videoAndPlanIdProvider
     */
    public function testShouldAcceptVideoUnassignFromPlanRequest(VideoId $videoId, PlanId $planId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoId);

        $response = $this->delete(
            route('videos.plans.unassign', ['videoId' => $videoId->toString(), 'planId' => $planId->toString()]),
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
