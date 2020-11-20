<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Exceptions\DomainException;

class ChildCannotBeDetachedFromParent extends DomainException
{
    public static function requestedParentDoesNotExists(): self
    {
        return new self('Requested parent does not exists.');
    }

    public static function requestedParentCurrentDoesNotHaveRequestedChild(): self
    {
        return new self('Requested parent currently does not have requested child.');
    }
}
