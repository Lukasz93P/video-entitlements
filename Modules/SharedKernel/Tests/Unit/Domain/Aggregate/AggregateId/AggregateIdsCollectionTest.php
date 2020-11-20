<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Tests\Unit\Domain\Aggregate\AggregateId;

use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateIdsCollection;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\UuidBasedAggregateId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AggregateIdsCollectionTest extends TestCase
{
    public function aggregateIdsProvider(): array
    {
        return [
            [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
            [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
            [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
            [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
        ];
    }

    public function multipleAggregateIdsPackagesProvider(): array
    {
        return [
            [
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
            ],
            [
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
                [UuidBasedAggregateId::generate(), UuidBasedAggregateId::generate()],
            ],
        ];
    }

    /**
     * @dataProvider aggregateIdsProvider
     */
    public function testShouldContainPreviouslyAddedId(AggregateId ...$aggregateIds): void
    {
        $testedAggregateIdsCollection = AggregateIdsCollection::create($aggregateIds);

        foreach ($aggregateIds as $aggregateId) {
            $this->assertTrue($testedAggregateIdsCollection->contains($aggregateId));
        }
    }

    /**
     * @dataProvider aggregateIdsProvider
     */
    public function testShouldNotContainAggregateIdWhichHasNotBenAdded(AggregateId ...$aggregateIds): void
    {
        $testedAggregateIdsCollection = AggregateIdsCollection::create($aggregateIds);

        $this->assertFalse($testedAggregateIdsCollection->contains(UuidBasedAggregateId::generate()));
    }

    public function testShouldBeEmptyWhenCreatedWithEmptyArray(): void
    {
        $this->assertTrue(AggregateIdsCollection::create([])->isEmpty());
    }

    /**
     * @dataProvider aggregateIdsProvider
     */
    public function testShouldNotBeEmptyWhenContainsAtLeastOneAggregateId(AggregateId $aggregateId): void
    {
        $this->assertFalse(AggregateIdsCollection::create([$aggregateId])->isEmpty());
    }

    /**
     * @dataProvider multipleAggregateIdsPackagesProvider
     */
    public function testMergedShouldContainIdFromBothCollections(
        array $aggregateIdsForFirstCollection,
        array $aggregateIdsForSecondCollection
    ): void {
        $firstCollection = AggregateIdsCollection::create($aggregateIdsForFirstCollection);
        $secondCollection = AggregateIdsCollection::create($aggregateIdsForSecondCollection);

        $mergedCollection = $firstCollection->merge($secondCollection);

        foreach (array_merge($aggregateIdsForFirstCollection, $aggregateIdsForSecondCollection) as $aggregateId) {
            $this->assertTrue($mergedCollection->contains($aggregateId));
        }
    }

    /**
     * @dataProvider multipleAggregateIdsPackagesProvider
     */
    public function testShouldReturnCollectionWithIntersectedObjects(
        array $aggregateIdsForFirstCollection,
        array $aggregateIdsForSecondCollection,
        array $sharedAggregateIds
    ): void {
        $firstCollection = AggregateIdsCollection::create(
            array_merge($aggregateIdsForFirstCollection, $sharedAggregateIds)
        );

        $secondCollection = AggregateIdsCollection::create(
            array_merge($aggregateIdsForSecondCollection, $sharedAggregateIds)
        );

        $intersectedCollection = $firstCollection->intersect($secondCollection);

        $notSharedAggregateIds = array_merge($aggregateIdsForFirstCollection, $aggregateIdsForSecondCollection);

        foreach ($notSharedAggregateIds as $notSharedAggregateId) {
            $this->assertFalse($intersectedCollection->contains($notSharedAggregateId));
        }

        foreach ($sharedAggregateIds as $sharedAggregateId) {
            $this->assertTrue($intersectedCollection->contains($sharedAggregateId));
        }
    }

    /**
     * @dataProvider aggregateIdsProvider
     */
    public function testShouldRemoveGivenAggregateId(AggregateId $aggregateId): void
    {
        $testedCollection = AggregateIdsCollection::create([$aggregateId]);

        $testedCollection->remove($aggregateId);

        $this->assertFalse($testedCollection->contains($aggregateId));
    }

    /**
     * @dataProvider aggregateIdsProvider
     */
    public function testShouldAddGivenAggregateId(AggregateId $aggregateId): void
    {
        $testedCollection = AggregateIdsCollection::create([]);

        $testedCollection->add($aggregateId);

        $this->assertTrue($testedCollection->contains($aggregateId));
    }
}
