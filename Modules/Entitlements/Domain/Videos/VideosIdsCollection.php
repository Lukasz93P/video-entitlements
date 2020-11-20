<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateIdsCollection;

class VideosIdsCollection extends AggregateIdsCollection
{
    protected static function allowedAggregateIdsClasses(): array
    {
        return [VideoId::class];
    }
}
