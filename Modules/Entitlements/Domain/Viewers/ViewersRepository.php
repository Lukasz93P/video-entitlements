<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

interface ViewersRepository
{
    public function add(Viewer $viewer): void;

    /**
     * @throws AggregateNotFound
     */
    public function find(ViewerId $id): Viewer;
}
