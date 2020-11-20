<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers\ViewerAggregate;

use Carbon\Carbon;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;

class PurchasedPlans
{
    private array $purchasedPlans;

    private function __construct(array $purchasedPlans)
    {
        $this->purchasedPlans = $purchasedPlans;
    }

    public static function create(): self
    {
        return new self([]);
    }

    public function add(PlanId $planId, string $expiresAt): void
    {
        $this->purchasedPlans[$planId->toString()] = $expiresAt;
    }

    public function activePlans(): array
    {
        $activePlansIds = [];

        foreach ($this->purchasedPlans as $planId => $expiresAt) {
            if (Carbon::parse($expiresAt)->isPast()) {
                continue;
            }

            $activePlansIds[] = PlanId::fromString($planId);
        }

        return $activePlansIds;
    }
}
