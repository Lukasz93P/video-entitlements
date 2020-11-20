<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\BroadcastersPlans;

use Modules\SharedKernel\Application\ApplicationCommand;

abstract class ChangePlanRelationship extends ApplicationCommand
{
    protected string $broadcasterId;

    protected string $childPlanId;

    protected string $parentPlanId;

    public function __construct(string $broadcasterId, string $childPlanId, string $parentPlanId)
    {
        $this->broadcasterId = $broadcasterId;
        $this->childPlanId = $childPlanId;
        $this->parentPlanId = $parentPlanId;
    }
}
