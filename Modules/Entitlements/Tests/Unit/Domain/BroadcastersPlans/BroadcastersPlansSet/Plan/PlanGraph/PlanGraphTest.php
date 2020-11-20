<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\PlanGraph;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\PlanGraph\PlanGraph;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlanGraphTest extends TestCase
{
    private PlanGraph $testedPlanGraph;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedPlanGraph = PlanGraph::create(PlanId::generate());
    }

    public function planIdsProvider(): array
    {
        return [
            [PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate()],
        ];
    }

    public function categoryAndPlansIdProvider(): array
    {
        return [
            [CategoryId::generate(), PlanId::generate(), PlanId::generate()],
            [CategoryId::generate(), PlanId::generate(), PlanId::generate()],
            [CategoryId::generate(), PlanId::generate(), PlanId::generate()],
        ];
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldNotFindChildWhenItWasNotAdded(PlanId $childId): void
    {
        $this->assertNull($this->testedPlanGraph->findChild($childId));
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldFindChildWhenItHasBeenAdded(PlanId $childId): void
    {
        $this->testedPlanGraph->associateChildWithParent(PlanGraph::create($childId), $this->testedPlanGraph->id());

        $this->assertTrue($this->testedPlanGraph->findChild($childId)->id()->equals($childId));
    }

    public function testShouldNotAllowToAddOwnParentAsChild(): void
    {
        $parentId = PlanId::generate();
        $parent = PlanGraph::create($parentId);

        $parent->associateChildWithParent($this->testedPlanGraph, $parentId);

        $this->expectException(ChildCannotBeAttachedToParent::class);

        $this->testedPlanGraph->associateChildWithParent($parent, $this->testedPlanGraph->id());
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldDetachChildWhenIsRequestedParentItself(PlanId $childId): void
    {
        $child = PlanGraph::create($childId);

        $this->testedPlanGraph->associateChildWithParent($child, $this->testedPlanGraph->id());

        $this->testedPlanGraph->detachChildFromParent($childId, $this->testedPlanGraph->id());

        $this->assertNull($this->testedPlanGraph->findChild($childId));
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldDetachIndirectChild(
        PlanId $testedPlanGraphChildId,
        PlanId $testedPlanGraphChildChildId
    ): void {
        $childOfTestedPlanGraph = PlanGraph::create($testedPlanGraphChildChildId);

        $childOfChildOfTestedPlanGraph = PlanGraph::create($testedPlanGraphChildId);

        $this->testedPlanGraph->associateChildWithParent($childOfTestedPlanGraph, $this->testedPlanGraph->id());
        $this->testedPlanGraph->associateChildWithParent($childOfChildOfTestedPlanGraph, $this->testedPlanGraph->id());

        $this->testedPlanGraph->detachChildFromParent($testedPlanGraphChildChildId, $testedPlanGraphChildChildId);

        $this->assertNull(
            $this->testedPlanGraph->findChild($testedPlanGraphChildChildId)->findChild($testedPlanGraphChildId)
        );
    }

    public function testShouldNotAllowToAddItselfAsChild(): void
    {
        $childToAdd = PlanGraph::create($this->testedPlanGraph->id());

        $this->expectException(ChildCannotBeAttachedToParent::class);

        $this->testedPlanGraph->associateChildWithParent($childToAdd, $this->testedPlanGraph->id());
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldAttachDirectChild(PlanId $childId): void
    {
        $childPlan = PlanGraph::create($childId);

        $this->testedPlanGraph->associateChildWithParent($childPlan, $this->testedPlanGraph->id());

        $this->assertTrue($this->testedPlanGraph->findChild($childId)->id()->equals($childId));
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldAttachIndirectChild(
        PlanId $testedPlanGraphChildId,
        PlanId $testedPlanGraphChildChildId
    ): void {
        $childOfTestedPlanGraph = PlanGraph::create($testedPlanGraphChildId);
        $childOfTestedPlanGraphChild = PlanGraph::create($testedPlanGraphChildChildId);

        $this->testedPlanGraph->associateChildWithParent($childOfTestedPlanGraph, $this->testedPlanGraph->id());
        $this->testedPlanGraph->associateChildWithParent($childOfTestedPlanGraphChild, $this->testedPlanGraph->id());

        $this->testedPlanGraph->associateChildWithParent(
            PlanGraph::create($testedPlanGraphChildChildId),
            $childOfTestedPlanGraph->id()
        );

        $hasChildOfChildBeenAttached = $this->testedPlanGraph
            ->findChild($testedPlanGraphChildId)
            ->findChild($testedPlanGraphChildChildId)
            ->id()
            ->equals($testedPlanGraphChildChildId)
        ;

        $this->assertTrue($hasChildOfChildBeenAttached);
    }

    /**
     * @dataProvider categoryAndPlansIdProvider
     */
    public function testShouldAssignCategoryToItself(CategoryId $categoryId): void
    {
        $this->testedPlanGraph->assignCategory($this->testedPlanGraph->id(), $categoryId);

        $this->assertTrue($this->testedPlanGraph->assignedCategoriesIds()->contains($categoryId));
    }

    /**
     * @dataProvider categoryAndPlansIdProvider
     */
    public function testShouldUnassignCategoryToItself(CategoryId $categoryId): void
    {
        $this->testedPlanGraph->assignCategory($this->testedPlanGraph->id(), $categoryId);

        $this->testedPlanGraph->unassignCategory($this->testedPlanGraph->id(), $categoryId);

        $this->assertFalse($this->testedPlanGraph->assignedCategoriesIds()->contains($categoryId));
    }

    /**
     * @dataProvider categoryAndPlansIdProvider
     */
    public function testShouldAssignCategoryToOneOfChildrenPlans(
        CategoryId $categoryId,
        PlanId $parentPlanId,
        PlanId $chilPlanId
    ): void {
        $this->testedPlanGraph->associateChildWithParent(
            PlanGraph::create($parentPlanId),
            $this->testedPlanGraph->id()
        );
        $this->testedPlanGraph->associateChildWithParent(PlanGraph::create($chilPlanId), $this->testedPlanGraph->id());

        $this->testedPlanGraph->associateChildWithParent(PlanGraph::create($chilPlanId), $parentPlanId);

        $this->testedPlanGraph->assignCategory($chilPlanId, $categoryId);

        $this->assertTrue(
            $this->testedPlanGraph->findChild($chilPlanId)->assignedCategoriesIds()->contains($categoryId)
        );

        /** @var PlanGraph $childFoundThroughParent */
        $childFoundThroughParent = $this->testedPlanGraph->findChild($parentPlanId)->findChild($chilPlanId);
        $this->assertTrue($childFoundThroughParent->assignedCategoriesIds()->contains($categoryId));
    }

    /**
     * @dataProvider categoryAndPlansIdProvider
     */
    public function testShouldUnassignCategoryFromOneOfChildrenPlans(
        CategoryId $categoryId,
        PlanId $parentPlanId,
        PlanId $chilPlanId
    ): void {
        $this->testedPlanGraph->associateChildWithParent(
            PlanGraph::create($parentPlanId),
            $this->testedPlanGraph->id()
        );
        $this->testedPlanGraph->associateChildWithParent(PlanGraph::create($chilPlanId), $this->testedPlanGraph->id());

        $this->testedPlanGraph->associateChildWithParent(PlanGraph::create($chilPlanId), $parentPlanId);

        $this->testedPlanGraph->assignCategory($chilPlanId, $categoryId);

        $this->testedPlanGraph->unassignCategory($chilPlanId, $categoryId);

        $this->assertFalse(
            $this->testedPlanGraph->findChild($chilPlanId)->assignedCategoriesIds()->contains($categoryId)
        );

        /** @var PlanGraph $childFoundThroughParent */
        $childFoundThroughParent = $this->testedPlanGraph->findChild($parentPlanId)->findChild($chilPlanId);
        $this->assertFalse($childFoundThroughParent->assignedCategoriesIds()->contains($categoryId));
    }
}
