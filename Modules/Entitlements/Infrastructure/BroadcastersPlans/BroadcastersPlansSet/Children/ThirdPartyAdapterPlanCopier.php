<?php

declare(strict_types=1);

namespace Modules\Entitlements\Infrastructure\BroadcastersPlans\BroadcastersPlansSet\Children;

use DeepCopy\DeepCopy;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\PlanCopier;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;

class ThirdPartyAdapterPlanCopier implements PlanCopier
{
    private DeepCopy $copier;

    private function __construct(DeepCopy $copier)
    {
        $this->copier = $copier;
    }

    public static function create(): PlanCopier
    {
        return new self(new DeepCopy());
    }

    public function copy(Plan $plan): Plan
    {
        return $this->copier->copy($plan);
    }
}
