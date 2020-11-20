<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Carbon\Carbon;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Modules\Entitlements\Tests\Fakes\FakesVideosRepository;
use Modules\Entitlements\Tests\Fakes\FakesViewersRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ViewerEntitlementToWatchVideoFeatureTest extends TestCase
{
    use FakesViewersRepository;
    use FakesVideosRepository;
    use FakesBroadcastersPlansRepository;

    public function viewerVideoAndBroadcasterIdsProvider(): array
    {
        return [
            [ViewerId::generate(), VideoId::generate(), BroadcasterId::generate()],
            [ViewerId::generate(), VideoId::generate(), BroadcasterId::generate()],
            [ViewerId::generate(), VideoId::generate(), BroadcasterId::generate()],
            [ViewerId::generate(), VideoId::generate(), BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldNotBeEntitledToWatchVideoIfNotPurchasedAnyPlans(
        ViewerId $viewerId,
        VideoId $videoId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId, $broadcasterId);
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId);

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldNotBeEntitledToWatchVideoIfNotPurchasedPlansToWhichVideoIsAssigned(
        ViewerId $viewerId,
        VideoId $videoId,
        BroadcasterId $broadcasterId
    ): void {
        $planId = PlanId::generate();

        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId, $broadcasterId);
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $planId);

        $this->put(
            route('videos.plans.assign', ['videoId' => $videoId->toString()]),
            ['planId' => $planId->toString()]
        );

        $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => PlanId::generate(), 'expiresAt' => Carbon::now()->addDays(1)->toDateTimeString()]
        );

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldBeEntitledToWatchVideoIfPurchasedPlansToWhichVideoIsDirectlyAssigned(
        ViewerId $viewerId,
        VideoId $videoId,
        BroadcasterId $broadcasterId
    ): void {
        $planId = PlanId::generate();

        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId, $broadcasterId);
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $planId);

        $this->put(
            route('videos.plans.assign', ['videoId' => $videoId->toString()]),
            ['planId' => $planId->toString()]
        );

        $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => $planId->toString(), 'expiresAt' => Carbon::now()->addDays(1)->toDateTimeString()]
        );

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldBeEntitledToWatchVideoIfPurchasedPlanToWhichVideoIsIndirectlyAssigned(
        ViewerId $viewerId,
        VideoId $videoId,
        BroadcasterId $broadcasterId
    ): void {
        $planId = PlanId::generate();
        $childPlanId = PlanId::generate();
        $childChildPlanId = PlanId::generate();

        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId, $broadcasterId);
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin(
            $broadcasterId,
            $planId,
            $childPlanId,
            $childChildPlanId
        );

        $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childPlanId->toString()]
            ),
            ['parentPlanId' => $planId->toString()]
        );

        $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childChildPlanId->toString()]
            ),
            ['parentPlanId' => $childPlanId->toString()]
        );

        $this->put(
            route('videos.plans.assign', ['videoId' => $videoId->toString()]),
            ['planId' => $planId->toString()]
        );

        $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => $planId->toString(), 'expiresAt' => Carbon::now()->addDays(1)->toDateTimeString()]
        );

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldBeEntitledToWatchVideoIfPurchasedItAsPayPerView(
        ViewerId $viewerId,
        VideoId $videoId
    ): void {
        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId);

        $this->put(
            route('viewers.videos.purchased', ['viewerId' => $viewerId->toString()]),
            ['videoId' => $videoId->toString()]
        );

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @dataProvider viewerVideoAndBroadcasterIdsProvider
     */
    public function testViewerShouldBeEntitledToWatchVideoIfPurchasedPlanToWithCategoryToWhichVideoIsAssigned(
        ViewerId $viewerId,
        VideoId $videoId,
        BroadcasterId $broadcasterId
    ): void {
        $planId = PlanId::generate();
        $childPlanId = PlanId::generate();
        $childChildPlanId = PlanId::generate();

        $videoCategoryId = CategoryId::generate();

        $this->fakeRepositoryContainsViewerWithId($viewerId);
        $this->fakeRepositoryContainsVideoWithId($videoId, $broadcasterId);
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin(
            $broadcasterId,
            $planId,
            $childPlanId,
            $childChildPlanId
        );

        $this->put(
            route(
                'broadcasters.plans.categories.assign',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childChildPlanId->toString()]
            ),
            ['categoryId' => $videoCategoryId->toString()]
        );

        $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childPlanId->toString()]
            ),
            ['parentPlanId' => $planId->toString()]
        );

        $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childChildPlanId->toString()]
            ),
            ['parentPlanId' => $childPlanId->toString()]
        );

        $this->put(
            route('videos.categories.assign', ['videoId' => $videoId->toString()]),
            ['categoryId' => $videoCategoryId->toString()]
        );

        $this->put(
            route('videos.categories.assign', ['videoId' => $videoId->toString()]),
            ['categoryId' => CategoryId::generate()->toString()]
        );

        $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => $planId->toString(), 'expiresAt' => Carbon::now()->addDays(1)->toDateTimeString()]
        );

        $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => PlanId::generate()->toString(), 'expiresAt' => Carbon::now()->addDays(1)->toDateTimeString()]
        );

        $response = $this->get(
            route(
                'videos.viewers.entitlement',
                ['videoId' => $videoId->toString(), 'viewerId' => $viewerId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_OK);
    }
}
