<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\BroadcastersPlans;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\BroadcastersPlans\RegisterNewPlan;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansFactory;
use Modules\Entitlements\Domain\BroadcastersPlans\NewPlanRegistered;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterNewPlanTest extends TestCase
{
    use FakesBroadcastersPlansRepository;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function planDataProvider(): array
    {
        return [
            [BroadcasterId::generate(), PlanId::generate()],
            [BroadcasterId::generate(), PlanId::generate()],
            [BroadcasterId::generate(), PlanId::generate()],
            [BroadcasterId::generate(), PlanId::generate()],
        ];
    }

    /**
     * @dataProvider planDataProvider
     */
    public function testShouldAddNewPlanForBroadcaster(BroadcasterId $broadcasterId, PlanId $planId): void
    {
        $this->fakeRepositoryContainsBroadcastersPlans(BroadcastersPlansFactory::create($broadcasterId));

        dispatch(new RegisterNewPlan($broadcasterId->toString(), $planId->toString()));

        Event::assertDispatched(
            NewPlanRegistered::class,
            static fn (NewPlanRegistered $event) => $event->broadcasterId() === $broadcasterId->toString()
                && $event->registeredPlanId() === $planId->toString()
        );
    }
}
