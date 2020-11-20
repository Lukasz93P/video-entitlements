<?php

declare(strict_types=1);

namespace Modules\Entitlements\Infrastructure\BroadcastersPlans;

use Illuminate\Support\Facades\Redis;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

class BroadcastersPlansRedisRepository implements BroadcastersPlansRepository
{
    private const KEY_SPACE = 'entitlements_broadcasters_plans';

    public function add(BroadcastersPlans $broadcastersPlans): void
    {
        Redis::set($this->buildKeyForBroadcaster($broadcastersPlans->id()), serialize($broadcastersPlans));
    }

    public function find(BroadcasterId $broadcasterId): BroadcastersPlans
    {
        $result = Redis::get($this->buildKeyForBroadcaster($broadcasterId));

        if (!$result) {
            throw AggregateNotFound::create();
        }

        return unserialize($result, [BroadcastersPlans::class]);
    }

    private function buildKeyForBroadcaster(BroadcasterId $broadcasterId): string
    {
        return self::KEY_SPACE."_{$broadcasterId->toString()}";
    }
}
