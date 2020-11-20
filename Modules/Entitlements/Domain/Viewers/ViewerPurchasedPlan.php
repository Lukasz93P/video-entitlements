<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class ViewerPurchasedPlan extends Event
{
    private string $planId;

    private string $expiresAt;

    public static function create(EventId $id, ViewerId $viewerId, PlanId $planId, string $expiresAt): self
    {
        $newInstance = self::raise($id, $viewerId);

        $newInstance->planId = $planId->toString();
        $newInstance->expiresAt = $expiresAt;

        return $newInstance;
    }

    public function planId(): string
    {
        return $this->planId;
    }

    public function viewerId(): string
    {
        return $this->sourceId();
    }

    public function expiresAt(): string
    {
        return $this->expiresAt;
    }
}
