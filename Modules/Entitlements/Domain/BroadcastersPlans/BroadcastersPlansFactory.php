<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\BroadcastersPlansSet;

final class BroadcastersPlansFactory
{
    private function __construct()
    {
    }

    public static function create(BroadcasterId $broadcasterId): BroadcastersPlans
    {
        return BroadcastersPlansSet::create($broadcasterId);
    }
}
