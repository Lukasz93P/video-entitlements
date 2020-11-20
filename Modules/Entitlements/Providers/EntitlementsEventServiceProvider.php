<?php

declare(strict_types=1);

namespace Modules\Entitlements\Providers;

use App\Providers\EventServiceProvider;
use Modules\Entitlements\Integration\Listeners\Plans\PlanRegisteringListener;
use Modules\Entitlements\Integration\Listeners\Videos\VideoRegisteringListener;
use Modules\Resources\Domain\Plans\NewPlanAdded;
use Modules\Resources\Domain\Videos\NewVideoAdded;

class EntitlementsEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        NewPlanAdded::class => [PlanRegisteringListener::class],
        NewVideoAdded::class => [VideoRegisteringListener::class],
    ];
}
