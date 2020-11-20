<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children;

use Modules\Entitlements\Infrastructure\BroadcastersPlans\BroadcastersPlansSet\Children\ThirdPartyAdapterPlanCopier;

final class PlanCopierFactory
{
    private function __construct()
    {
    }

    public static function create(): PlanCopier
    {
        return ThirdPartyAdapterPlanCopier::create();
    }
}
