<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate\AggregateId;

use Illuminate\Support\Str;
use InvalidArgumentException;

class UuidBasedAggregateId implements AggregateId
{
    protected string $id;

    final private function __construct(string $id)
    {
        if (!Str::isUuid($id)) {
            throw new InvalidArgumentException('Id have to be valid uuid.');
        }

        $this->id = $id;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return static
     */
    public static function generate(): AggregateId
    {
        return new static(Str::uuid()->toString());
    }

    /**
     * @return static
     */
    public static function fromString(string $string): AggregateId
    {
        return new static($string);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals(AggregateId $otherAggregateId): bool
    {
        return $this->toString() === $otherAggregateId->toString();
    }
}
