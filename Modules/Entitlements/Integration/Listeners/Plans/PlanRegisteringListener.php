<?php

declare(strict_types=1);

namespace Modules\Entitlements\Integration\Listeners\Plans;

use Modules\Entitlements\Application\BroadcastersPlans\RegisterNewPlan;
use Modules\Resources\Domain\Plans\NewPlanAdded;

class PlanRegisteringListener
{
    public function handle(NewPlanAdded $newPlanAddedEvent): void
    {
        dispatch(new RegisterNewPlan($newPlanAddedEvent->broadcasterId(), $newPlanAddedEvent->planId()));
    }
}
