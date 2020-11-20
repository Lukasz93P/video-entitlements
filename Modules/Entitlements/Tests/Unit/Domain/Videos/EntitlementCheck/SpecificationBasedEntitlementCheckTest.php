<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\Videos\EntitlementCheck;

use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlans;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcastersPlansRepository;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanIdsCollection;
use Modules\Entitlements\Domain\Videos\EntitlementCheck\SpecificationBasedEntitlementCheck;
use Modules\Entitlements\Domain\Videos\EntitlementSpecification;
use Modules\Entitlements\Domain\Videos\Video;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\Viewer;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SpecificationBasedEntitlementCheckTest extends TestCase
{
    /**
     * @var BroadcastersPlansRepository|MockObject
     */
    private MockObject $broadcastersPlansRepositoryMock;

    /**
     * @var MockObject|Video
     */
    private MockObject $videoMock;

    /**
     * @var MockObject|Viewer
     */
    private MockObject $viewerMock;

    /**
     * @var BroadcastersPlans|MockObject
     */
    private MockObject $broadcastersPlansMock;

    /**
     * @var EntitlementSpecification|MockObject
     */
    private MockObject $entitlementSpecificationMock;

    private SpecificationBasedEntitlementCheck $testedEntitlementCheck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->broadcastersPlansRepositoryMock = $this->getMockBuilder(BroadcastersPlansRepository::class)->getMock();
        $this->videoMock = $this->getMockBuilder(Video::class)->getMock();
        $this->videoMock->method('id')->willReturn(VideoId::generate());
        $this->viewerMock = $this->getMockBuilder(Viewer::class)->getMock();
        $this->broadcastersPlansMock = $this->getMockBuilder(BroadcastersPlans::class)->getMock();
        $this->entitlementSpecificationMock = $this
            ->getMockBuilder(EntitlementSpecification::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->videoMock->method('createEntitlementSpecification')->willReturn($this->entitlementSpecificationMock);

        $this->testedEntitlementCheck = new SpecificationBasedEntitlementCheck($this->broadcastersPlansRepositoryMock);
    }

    public function broadcasterAndPlansIdsProvider(): array
    {
        return [
            [BroadcasterId::generate(), PlanId::generate(), PlanId::generate(), PlanId::generate()],
            [BroadcasterId::generate(), PlanId::generate(), PlanId::generate()],
            [BroadcasterId::generate(), PlanId::generate(), PlanId::generate(), PlanId::generate()],
        ];
    }

    public function testShouldTellThatViewerIsEntitledToWatchVideoIfPurchasedItAsPayPerView(): void
    {
        $this->viewerMock->method('hasPurchasedAsPayPerView')->with($this->videoMock->id())->willReturn(true);

        $this->assertTrue($this->testedEntitlementCheck->isEntitledToWatch($this->videoMock, $this->viewerMock));
    }

    /**
     * @dataProvider broadcasterAndPlansIdsProvider
     */
    public function testShouldTellThatViewerIsEntitledToWatchVideoIfOneOfHisPlansMeetsSpecification(
        BroadcasterId $broadcasterId,
        PlanId ...$planIds
    ): void {
        $this->videoMock->method('broadcasterId')->willReturn($broadcasterId);

        $this->broadcastersPlansRepositoryMock
            ->method('find')
            ->with($broadcasterId)
            ->willReturn($this->broadcastersPlansMock)
        ;

        $this->viewerMock->method('purchasedActivePlansIds')->willReturn(PlanIdsCollection::create($planIds));

        $results = [...array_map(fn () => false, range(0, count($planIds) - 2)), true];

        $this->broadcastersPlansMock
            ->method('doesPlanMeet')
            ->withConsecutive(
                ...array_map(
                    fn (PlanId $planId) => [$planId, $this->entitlementSpecificationMock],
                    $planIds
                ),
            )
            ->willReturnOnConsecutiveCalls(...$results)
        ;

        $this->assertTrue($this->testedEntitlementCheck->isEntitledToWatch($this->videoMock, $this->viewerMock));
    }
}
