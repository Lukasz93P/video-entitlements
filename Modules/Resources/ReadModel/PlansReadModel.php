<?php

declare(strict_types=1);

namespace Modules\Resources\ReadModel;

use Illuminate\Support\Facades\Redis;
use Modules\Resources\Domain\Plans\NewPlanAdded;

class PlansReadModel
{
    public const BROADCASTERS_PLANS_IDS_KEY_SPACE = 'resources_broadcasters_plans_ids';

    /**
     * @return string[]
     */
    public function getBroadcastersPlansIds(string $broadcasterId): array
    {
        $broadcastersPlansIds = Redis::get(self::BROADCASTERS_PLANS_IDS_KEY_SPACE.$broadcasterId);

        return json_decode($broadcastersPlansIds ?? '[]', true);
    }

    public function handle($planEvent): void
    {
        if ($planEvent instanceof NewPlanAdded) {
            $this->actualizeBroadcastersPlansIds($planEvent);
        }
    }

    private function actualizeBroadcastersPlansIds(NewPlanAdded $newPlanAdded): void
    {
        $key = self::BROADCASTERS_PLANS_IDS_KEY_SPACE.$newPlanAdded->broadcasterId();

        $currentBroadcastersPlansIds = Redis::get($key);

        $actualizedBroadcastersPlansIds = array_merge(
            $currentBroadcastersPlansIds ? json_decode($currentBroadcastersPlansIds, true) : [],
            [$newPlanAdded->planId()]
        );

        Redis::set($key, json_encode($actualizedBroadcastersPlansIds));
    }
}
