<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\Videos\Video;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;
use Modules\Entitlements\Domain\Videos\Video\VideoAggregate;
use Modules\Entitlements\Domain\Videos\VideoId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VideoAggregateTest extends TestCase
{
    private VideoAggregate $testedVideo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedVideo = VideoAggregate::create(VideoId::generate(), BroadcasterId::generate());
    }

    public function planIdProvider(): array
    {
        return [
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
        ];
    }

    public function categoriesIdsProvider(): array
    {
        return [
            [CategoryId::generate(), CategoryId::generate(), CategoryId::generate()],
            [CategoryId::generate(), CategoryId::generate(), CategoryId::generate()],
            [CategoryId::generate(), CategoryId::generate(), CategoryId::generate()],
            [CategoryId::generate(), CategoryId::generate(), CategoryId::generate()],
        ];
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldBeAssignedToProperPlans(PlanId ...$plansToAssign): void
    {
        foreach ($plansToAssign as $planId) {
            $this->testedVideo->assignToPlan($planId);
        }

        $assignedPlans = $this->testedVideo->assignedPlansIds();

        foreach ($plansToAssign as $planId) {
            $this->assertTrue($assignedPlans->contains($planId));
        }
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldNotBeAssignedToPlanToWhichHasNotBeenDirectlyAssigned(PlanId $planId): void
    {
        $this->assertFalse($this->testedVideo->assignedPlansIds()->contains($planId));
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldNotBeenAssignedToPlanFromWhichHasBeenUnassigned(PlanId $planId): void
    {
        $this->testedVideo->assignToPlan($planId);

        $this->testedVideo->unassignFromPlan($planId);

        $this->assertFalse($this->testedVideo->assignedPlansIds()->contains($planId));
    }

    /**
     * @dataProvider categoriesIdsProvider
     */
    public function testShouldHaveAssignedCategories(CategoryId ...$categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $this->testedVideo->assignToCategory($categoryId);
        }

        foreach ($categoryIds as $categoryId) {
            $this->assertTrue(
                $this->testedVideo->assignedCategoriesIds()->contains($categoryId)
            );
        }
    }

    /**
     * @dataProvider categoriesIdsProvider
     */
    public function testShouldNotHaveUnassignedCategories(CategoryId ...$categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $this->testedVideo->assignToCategory($categoryId);
        }

        $categoryToUnassignId = $categoryIds[random_int(0, $this->count($categoryIds) - 1)];

        $this->testedVideo->unassignFromCategory($categoryToUnassignId);

        foreach ($categoryIds as $categoryId) {
            if (!$categoryId->equals($categoryToUnassignId)) {
                $this->assertTrue($this->testedVideo->assignedCategoriesIds()->contains($categoryId));
            }
        }

        $this->assertFalse($this->testedVideo->assignedCategoriesIds()->contains($categoryToUnassignId));
    }

    /**
     * @dataProvider categoriesIdsProvider
     */
    public function testEveryCategoryIdAddedToVideoShouldMeetEntitlementSpecification(CategoryId ...$categoryIds): void
    {
        foreach ($categoryIds as $categoryId) {
            $this->testedVideo->assignToCategory($categoryId);
        }

        $entitlementSpecification = $this->testedVideo->createEntitlementSpecification();

        $planIdsCollection = PlanIdsCollection::create([]);
        foreach ($categoryIds as $categoryId) {
            $this->assertTrue($entitlementSpecification->meets($planIdsCollection, CategoryIdsCollection::create([$categoryId])));
        }
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testEveryPlanIdAddedToVideoShouldMeetEntitlementSpecification(PlanId ...$planIds): void
    {
        foreach ($planIds as $planId) {
            $this->testedVideo->assignToPlan($planId);
        }

        $entitlementSpecification = $this->testedVideo->createEntitlementSpecification();

        $categoriesIdsCollection = CategoryIdsCollection::create([]);
        foreach ($planIds as $planId) {
            $this->assertTrue(
                $entitlementSpecification->meets(PlanIdsCollection::create([$planId]), $categoriesIdsCollection)
            );
        }
    }
}
