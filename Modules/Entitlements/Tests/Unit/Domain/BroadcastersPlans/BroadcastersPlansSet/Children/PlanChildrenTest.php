<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\BroadcastersPlans\BroadcastersPlansSet\Children;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Children\PlanChildren;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansSet\Plan\Plan;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PlanChildrenTest extends TestCase
{
    private PlanChildren $testedPlanChildren;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedPlanChildren = PlanChildren::create();
    }

    public function plansProvider(): array
    {
        return [
            [$this->createPlanMock(), $this->createPlanMock()],
            [$this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock()],
            [$this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock()],
        ];
    }

    public function planIdProvider(): array
    {
        return [
            [PlanId::generate()],
            [PlanId::generate()],
            [PlanId::generate()],
        ];
    }

    /**
     * @dataProvider plansProvider
     */
    public function testShouldFindPreviouslyDirectlyAttachedChildren(Plan ...$plans): void
    {
        foreach ($plans as $plan) {
            $this->testedPlanChildren->attachDirectChild($plan);
        }

        foreach ($plans as $plan) {
            $foundChild = $this->testedPlanChildren->findChild($plan->id());
            $this->assertEquals($plan->id()->toString(), $foundChild->id()->toString());
        }
    }

    /**
     * @dataProvider plansProvider
     */
    public function testShouldNotFindDirectChildWhichHasBeenDetached(Plan ...$plans): void
    {
        foreach ($plans as $plan) {
            $this->testedPlanChildren->attachDirectChild($plan);
        }

        $planToDetach = array_shift($plans);
        $this->testedPlanChildren->detachDirectChild($planToDetach->id());

        foreach ($plans as $plan) {
            $foundChild = $this->testedPlanChildren->findChild($plan->id());
            $this->assertEquals($plan->id()->toString(), $foundChild->id()->toString());
        }

        $this->assertEmpty($this->testedPlanChildren->findChild($planToDetach->id()));
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldReturnNullWhenChildNotFound(PlanId $searchedChildId): void
    {
        $plans = [$this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock()];

        foreach ($plans as $plan) {
            $this->testedPlanChildren->attachDirectChild($plan);
        }

        $this->assertNull($this->testedPlanChildren->findChild($searchedChildId));
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldReturnChildFoundWithinOwnChildren(PlanId $searchedChildId): void
    {
        $plans = [$this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock(), $this->createPlanMock()];

        $planChildMock = $this->createPlanMock();

        $plans[random_int(0, count($plans) - 1)]->method('findChild')->willReturn($planChildMock);

        foreach ($plans as $plan) {
            $this->testedPlanChildren->attachDirectChild($plan);
        }

        $this->assertSame($planChildMock, $this->testedPlanChildren->findChild($searchedChildId));
    }

    /**
     * @return MockObject|Plan
     */
    private function createPlanMock(string $planMockIdStringRepresentation = ''): Plan
    {
        $planMock = $this->getMockBuilder(Plan::class)->getMock();

        $planMockId = $planMockIdStringRepresentation
            ? PlanId::fromString($planMockIdStringRepresentation)
            : PlanId::generate();

        $planMock->method('id')->willReturn($planMockId);

        return $planMock;
    }
}
