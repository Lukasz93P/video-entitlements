<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlansCategoriesManagementFeatureTest extends TestCase
{
    use FakesBroadcastersPlansRepository;

    public function broadcasterPlanAndCategoryIdsProvider(): array
    {
        return [
            [BroadcasterId::generate(), PlanId::generate(), CategoryId::generate()],
            [BroadcasterId::generate(), PlanId::generate(), CategoryId::generate()],
            [BroadcasterId::generate(), PlanId::generate(), CategoryId::generate()],
            [BroadcasterId::generate(), PlanId::generate(), CategoryId::generate()],
        ];
    }

    /**
     * @dataProvider broadcasterPlanAndCategoryIdsProvider
     */
    public function testShouldAcceptAssignCategoryToPlanRequest(
        BroadcasterId $broadcasterId,
        PlanId $planId,
        CategoryId $categoryId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $planId);

        $response = $this->put(
            route(
                'broadcasters.plans.categories.assign',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $planId->toString()]
            ),
            ['categoryId' => $categoryId->toString()]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @dataProvider broadcasterPlanAndCategoryIdsProvider
     */
    public function testShouldAcceptUnassignCategoryToPlanRequest(
        BroadcasterId $broadcasterId,
        PlanId $planId,
        CategoryId $categoryId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $planId);

        $response = $this->delete(
            route(
                'broadcasters.plans.categories.unassign',
                [
                    'broadcasterId' => $broadcasterId->toString(),
                    'planId' => $planId->toString(),
                    'categoryId' => $categoryId->toString(),
                ]
            )
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
