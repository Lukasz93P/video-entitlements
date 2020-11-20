<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Viewers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\Viewer\RegisterThatViewerPurchasedPlan;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewerPurchasedPlan;
use Modules\Entitlements\Tests\Fakes\FakesViewersRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterThatViewerPurchasedPlanTest extends TestCase
{
    use FakesViewersRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function viewerPlanPurchaseDataProvider(): array
    {
        return [
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->addMonths(1)->toDateTimeString()],
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->addDays(2)->toDateTimeString()],
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->addYears(3)->toDateTimeString()],
            [ViewerId::generate(), PlanId::generate(), Carbon::now()->toDateTimeString()],
        ];
    }

    /**
     * @dataProvider viewerPlanPurchaseDataProvider
     */
    public function testShouldRegisterThatViewerPurchasedPlan(
        ViewerId $viewerId,
        PlanId $planId,
        string $expiresAt
    ): void {
        $this->fakeRepositoryContainsViewerWithId($viewerId);

        dispatch_now(new RegisterThatViewerPurchasedPlan($viewerId->toString(), $planId->toString(), $expiresAt));

        Event::assertDispatched(
            ViewerPurchasedPlan::class,
            fn (ViewerPurchasedPlan $event) => $event->viewerId() === $viewerId->toString()
                && $event->planId() === $planId->toString() && $event->expiresAt() === $expiresAt
        );
    }
}
