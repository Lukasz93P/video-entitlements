<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

interface BroadcastersPlansRepository
{
    public function add(BroadcastersPlans $broadcastersPlans): void;

    /**
     * @throws AggregateNotFound
     */
    public function find(BroadcasterId $broadcasterId): BroadcastersPlans;
}
