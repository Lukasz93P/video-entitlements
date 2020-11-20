<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet;

use Modules\SharedKernel\Domain\Exceptions\DomainException;

class DirectChildAlreadyAttached extends DomainException
{
    public static function create(): self
    {
        return new self('Direct child already attached.');
    }
}
