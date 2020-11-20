<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Exceptions\DomainException;

class PlanCannotBeRegistered extends DomainException
{
    public static function planAlreadyAdded(PlanId $planId): self
    {
        return new self("Plan with id {$planId->toString()} has already been registered.");
    }
}
