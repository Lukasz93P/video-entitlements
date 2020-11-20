<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\BroadcastersPlans\BroadcastersPlansSet;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\BroadcastersPlansSet;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanCannotBeRegistered;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Categories\CategoryIdsCollection;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BroadcastersPlansSetTest extends TestCase
{
    /**
     * @var EntitlementSpecification|MockObject
     */
    private MockObject $entitlementSpecificationMock;

    private BroadcastersPlansSet $testedBroadcastersPlansSet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entitlementSpecificationMock = $this
            ->getMockBuilder(EntitlementSpecification::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->testedBroadcastersPlansSet = BroadcastersPlansSet::create(BroadcasterId::generate());
    }

    public function planIdsProvider(): array
    {
        return [
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [PlanId::generate(), PlanId::generate(), PlanId::generate()],
        ];
    }

    public function planIdsAndMeetingRequirementDesiredOutcomeProvider(): array
    {
        return [
            [PlanId::generate(), true],
            [PlanId::generate(), false],
            [PlanId::generate(), false],
            [PlanId::generate(), true],
        ];
    }

    public function planAndCategoryIdProvider(): array
    {
        return [
            [PlanId::generate(), CategoryId::generate()],
            [PlanId::generate(), CategoryId::generate()],
            [PlanId::generate(), CategoryId::generate()],
            [PlanId::generate(), CategoryId::generate()],
        ];
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldNotAllowToAddExistingPlanAsNew(PlanId $newPlanId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($newPlanId);

        $this->expectException(PlanCannotBeRegistered::class);

        $this->testedBroadcastersPlansSet->registerNewPlan($newPlanId);
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldAddNewPlan(PlanId $newPlanId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($newPlanId);

        $this->assertTrue($this->testedBroadcastersPlansSet->findPlan($newPlanId)->id()->equals($newPlanId));
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldDetachChildFromParent(PlanId $childId, PlanId $parentId, PlanId $parentParentId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($childId);
        $this->testedBroadcastersPlansSet->registerNewPlan($parentId);
        $this->testedBroadcastersPlansSet->registerNewPlan($parentParentId);

        $this->testedBroadcastersPlansSet->attachChildToParent($childId, $parentId);
        $this->testedBroadcastersPlansSet->attachChildToParent($parentId, $parentParentId);

        $this->testedBroadcastersPlansSet->detachChildFromParent($childId, $parentId);

        $this->assertNull($this->testedBroadcastersPlansSet->findPlan($parentId)->findChild($childId));

        $this->assertNull(
            $this->testedBroadcastersPlansSet->findPlan($parentParentId)->findChild($parentId)->findChild($childId)
        );
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldNotAllowToAttachNonExistingPlanToParent(PlanId $childId, PlanId $parentId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($childId);

        $this->expectException(ChildCannotBeAttachedToParent::class);

        $this->testedBroadcastersPlansSet->attachChildToParent($childId, $parentId);
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldNotAllowToAttachChildToNonExistingParent(PlanId $childId, PlanId $parentId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($childId);

        $this->expectException(ChildCannotBeAttachedToParent::class);

        $this->testedBroadcastersPlansSet->attachChildToParent($childId, $parentId);
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldAddChildToRequestedParent(PlanId $childId, PlanId $parentId, PlanId $parentParentId): void
    {
        $this->testedBroadcastersPlansSet->registerNewPlan($childId);
        $this->testedBroadcastersPlansSet->registerNewPlan($parentId);
        $this->testedBroadcastersPlansSet->registerNewPlan($parentParentId);

        $this->testedBroadcastersPlansSet->attachChildToParent($parentId, $parentParentId);
        $this->testedBroadcastersPlansSet->attachChildToParent($childId, $parentId);

        $this->assertTrue(
            $this->testedBroadcastersPlansSet->findPlan($parentId)->findChild($childId)->id()->equals($childId)
        );

        $this->assertTrue(
            $this->testedBroadcastersPlansSet
                ->findPlan($parentParentId)
                ->findChild($parentId)
                ->findChild($childId)
                ->id()
                ->equals($childId)
        );
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testNonExistingPlanShouldNotMeetSpecification(PlanId $planId): void
    {
        $this->assertFalse(
            $this->testedBroadcastersPlansSet->doesPlanMeet($planId, $this->entitlementSpecificationMock)
        );
    }

    /**
     * @dataProvider planAndCategoryIdProvider
     */
    public function testShouldTellThatPlansMeetsSpecificationIfPlansCategoriesMeetIt(
        PlanId $planId,
        CategoryId $categoryId
    ): void {
        $this->testedBroadcastersPlansSet->registerNewPlan($planId);
        $this->testedBroadcastersPlansSet->assignCategoryToPlan($planId, $categoryId);

        $this->entitlementSpecificationMock
            ->method('meets')
            ->with(
                $this->callback(fn (PlanIdsCollection $collection) => $collection->contains($planId)),
                $this->callback(fn (CategoryIdsCollection $collection) => $collection->contains($categoryId))
            )
            ->willReturn(true)
        ;

        $this->assertTrue(
            $this->testedBroadcastersPlansSet->doesPlanMeet($planId, $this->entitlementSpecificationMock)
        );
    }

    /**
     * @dataProvider planIdsProvider
     */
    public function testShouldTellThatPlansMeetsSpecificationIfPlansChildrenMeetIt(
        PlanId $planId,
        PlanId $childPlanId
    ): void {
        $this->testedBroadcastersPlansSet->registerNewPlan($planId);
        $this->testedBroadcastersPlansSet->registerNewPlan($childPlanId);

        $this->testedBroadcastersPlansSet->attachChildToParent($childPlanId, $planId);

        $this->entitlementSpecificationMock
            ->method('meets')
            ->withConsecutive(
                [
                    $this->callback(fn (PlanIdsCollection $collection) => $collection->contains($planId)),
                    $this->anything(),
                ],
                [
                    $this->callback(fn (PlanIdsCollection $collection) => $collection->contains($childPlanId)),
                    $this->anything(),
                ]
            )
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $this->assertTrue(
            $this->testedBroadcastersPlansSet->doesPlanMeet($planId, $this->entitlementSpecificationMock)
        );
    }
}
