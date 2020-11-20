<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate\AggregateId;

interface AggregateId
{
    /**
     * @return static
     */
    public static function fromString(string $string): AggregateId;

    public function toString(): string;

    public function equals(AggregateId $otherAggregateId): bool;
}
