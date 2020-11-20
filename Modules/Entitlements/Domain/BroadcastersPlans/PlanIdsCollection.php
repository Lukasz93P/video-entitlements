<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateIdsCollection;

class PlanIdsCollection extends AggregateIdsCollection
{
    protected static function allowedAggregateIdsClasses(): array
    {
        return [PlanId::class];
    }
}
