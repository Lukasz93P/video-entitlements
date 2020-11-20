<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Carbon\Carbon;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Tests\Fakes\FakesViewersRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ViewerPlanPurchaseRegistrationFeatureTest extends TestCase
{
    use FakesViewersRepository;

    public function viewerPlanPurchaseDataProvider(): array
    {
        return [
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->toDateTimeString()],
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->addDays(3)->toDateTimeString()],
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->addMonths(4)->toDateTimeString()],
        ];
    }

    /**
     * @dataProvider viewerPlanPurchaseDataProvider
     */
    public function testShouldAcceptViewerPlanPurchaseRegistrationRequest(
        ViewerId $viewerId,
        PlanId $planId,
        string $expiresAt
    ): void {
        $this->fakeRepositoryContainsViewerWithId($viewerId);

        $response = $this->put(
            route(
                'viewers.plans.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['planId' => $planId->toString(), 'expiresAt' => $expiresAt]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
