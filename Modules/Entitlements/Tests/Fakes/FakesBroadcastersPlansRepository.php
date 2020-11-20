<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Fakes;

use Mockery;
use Mockery\Mock;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansFactory;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;

trait FakesBroadcastersPlansRepository
{
    private function fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin(
        BroadcasterId $broadcasterId,
        PlanId ...$plansId
    ): void {
        $broadcastersPlans = BroadcastersPlansFactory::create($broadcasterId);

        foreach ($plansId as $planId) {
            $broadcastersPlans->registerNewPlan($planId);
        }

        $this->fakeRepositoryContainsBroadcastersPlans($broadcastersPlans);
    }

    private function fakeRepositoryContainsBroadcastersPlans(BroadcastersPlans $broadcastersPlans): void
    {
        $this->instance(
            BroadcastersPlansRepository::class,
            Mockery::mock(
                BroadcastersPlansRepository::class,
                /** @var Mock $mock */
                function ($mock) use ($broadcastersPlans) {
                    $mock
                        ->shouldReceive('find')
                        ->andReturn($broadcastersPlans)
                    ;
                    $mock->shouldReceive('add');
                }
            )
        );
    }
}
