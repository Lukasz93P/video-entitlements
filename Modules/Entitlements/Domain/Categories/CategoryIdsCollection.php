<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Categories;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateIdsCollection;

class CategoryIdsCollection extends AggregateIdsCollection
{
    protected static function allowedAggregateIdsClasses(): array
    {
        return [CategoryId::class];
    }
}
