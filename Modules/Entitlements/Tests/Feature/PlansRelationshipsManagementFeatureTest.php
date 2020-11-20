<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlansRelationshipsManagementFeatureTest extends TestCase
{
    use FakesBroadcastersPlansRepository;

    public function childPlanParentPlanAndBroadcasterIdsProvider(): array
    {
        return [
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldAcceptPlanAttachingToParentRequest(
        PlanId $childPlanId,
        PlanId $parentPlanId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin(
            $broadcasterId,
            $childPlanId,
            $parentPlanId
        );

        $response = $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childPlanId->toString()]
            ),
            ['parentPlanId' => $parentPlanId->toString()]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldAcceptPlanDetachingFromParentRequestAfterAttachingIt(
        PlanId $childPlanId,
        PlanId $parentPlanId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin(
            $broadcasterId,
            $childPlanId,
            $parentPlanId
        );

        $this->put(
            route(
                'broadcasters.plans.parents.attach',
                ['broadcasterId' => $broadcasterId->toString(), 'planId' => $childPlanId->toString()]
            ),
            ['parentPlanId' => $parentPlanId->toString()]
        );

        $response = $this->delete(
            route(
                'broadcasters.plans.parents.detach',
                [
                    'broadcasterId' => $broadcasterId->toString(),
                    'planId' => $childPlanId->toString(),
                    'parentPlanId' => $parentPlanId->toString(),
                ]
            )
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
