<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Aggregate\AggregateId;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class AggregateIdsCollection implements Traversable, IteratorAggregate
{
    /**
     * @var AggregateId[]
     */
    private array $aggregateIds;

    /**
     * @param AggregateId[] $aggregateIds
     */
    private function __construct(array $aggregateIds)
    {
        $this->aggregateIds = $aggregateIds;
    }

    /**
     * @param AggregateId[] $aggregateIds
     */
    public static function create(array $aggregateIds): self
    {
        $keyedAggregateIds = [];

        foreach ($aggregateIds as $aggregateId) {
            self::validateAggregateId($aggregateId);
            $keyedAggregateIds[$aggregateId->toString()] = $aggregateId;
        }

        return new static($keyedAggregateIds);
    }

    public function contains(AggregateId $id): bool
    {
        return isset($this->aggregateIds[$id->toString()]);
    }

    public function merge(AggregateIdsCollection $aggregateIdsCollection): self
    {
        return static::create(array_merge($this->aggregateIds, $aggregateIdsCollection->aggregateIds));
    }

    public function intersect(AggregateIdsCollection $aggregateIdsCollection): self
    {
        return static::create(array_intersect_assoc($this->aggregateIds, $aggregateIdsCollection->aggregateIds));
    }

    public function isEmpty(): bool
    {
        return empty($this->aggregateIds);
    }

    public function remove(AggregateId $aggregateId): void
    {
        unset($this->aggregateIds[$aggregateId->toString()]);
    }

    public function add(AggregateId $aggregateId): void
    {
        self::validateAggregateId($aggregateId);

        $this->aggregateIds[$aggregateId->toString()] = $aggregateId;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->aggregateIds);
    }

    protected static function allowedAggregateIdsClasses(): array
    {
        return [];
    }

    private static function validateAggregateId(AggregateId $aggregateId): void
    {
        $allowedAggregateIdsClasses = self::allowedAggregateIdsClasses();

        if (!$allowedAggregateIdsClasses) {
            return;
        }

        if (!in_array(get_class($aggregateId), self::allowedAggregateIdsClasses(), true)) {
            throw new InvalidArgumentException(
                'Collection accepts only '.implode(', ', $allowedAggregateIdsClasses).
                get_class($aggregateId).' given.'
            );
        }
    }
}
