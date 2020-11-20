<?php

declare(strict_types=1);

namespace Modules\Resources\Providers;

use App\Providers\EventServiceProvider;
use Modules\Resources\Domain\Plans\NewPlanAdded;
use Modules\Resources\Domain\Videos\NewVideoAdded;
use Modules\Resources\ReadModel\PlansReadModel;
use Modules\Resources\ReadModel\VideosReadModel;

class ResourcesEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        NewPlanAdded::class => [PlansReadModel::class],
        NewVideoAdded::class => [VideosReadModel::class],
    ];
}
