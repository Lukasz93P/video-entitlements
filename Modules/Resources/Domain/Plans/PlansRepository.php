<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Plans;

interface PlansRepository
{
    public function add(Plan $plan): void;
}
