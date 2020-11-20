<?php

declare(strict_types=1);

namespace Modules\Resources\Infrastructure\Plans;

use Illuminate\Support\Facades\Redis;
use Modules\Resources\Domain\Plans\Plan;
use Modules\Resources\Domain\Plans\PlanId;
use Modules\Resources\Domain\Plans\PlansRepository;

class PlansRedisRepository implements PlansRepository
{
    private const KEY_SPACE = 'resource_plans';

    public function add(Plan $plan): void
    {
        Redis::set($this->buildKeyForPlan($plan->id()), serialize($plan));
    }

    private function buildKeyForPlan(PlanId $id): string
    {
        return self::KEY_SPACE."_{$id->toString()}";
    }
}
