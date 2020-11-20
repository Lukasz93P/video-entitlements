<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos;

use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;

interface Video extends Aggregate
{
    /**
     * @return VideoId
     */
    public function id(): AggregateId;

    public function changeTitle(Title $newTitle): void;
}
