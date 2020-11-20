<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans;

use Modules\SharedKernel\Domain\Exceptions\DomainException;

class ChildCannotBeAttachedToParent extends DomainException
{
    public static function currentParentCannotBeAddedAsChild(): self
    {
        return new self('Current parent of a plan cannot be added as its child.');
    }

    public static function planCannotBeChildOfItself(): self
    {
        return new self('Plan cannot be a child of itself.');
    }

    public static function childNotFound(): self
    {
        return new self('Requested child not found.');
    }

    public static function requestedParentDoesNotExists(): self
    {
        return new self('Requested parent does not exists.');
    }

    public static function planIsAlreadyDirectChildOfRequestedParent(): self
    {
        return new self('Plan is already a direct child of requested parent.');
    }
}
