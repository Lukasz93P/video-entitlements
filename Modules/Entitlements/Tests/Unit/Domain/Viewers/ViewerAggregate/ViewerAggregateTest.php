<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Unit\Domain\Viewers\ViewerAggregate;

use Carbon\Carbon;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\ViewerAggregate\ViewerAggregate;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ViewerAggregateTest extends TestCase
{
    private ViewerAggregate $testedViewerAggregate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedViewerAggregate = ViewerAggregate::create(ViewerId::generate());
    }

    public function planIdProvider(): array
    {
        return [
            [PlanId::generate()],
            [PlanId::generate()],
            [PlanId::generate()],
        ];
    }

    public function videoIdsProvider(): array
    {
        return [
            [VideoId::generate(), VideoId::generate()],
            [VideoId::generate(), VideoId::generate()],
            [VideoId::generate(), VideoId::generate()],
            [VideoId::generate(), VideoId::generate()],
        ];
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldHavePurchasedNotExpiredPlanAsActive(PlanId $planId): void
    {
        $this->testedViewerAggregate->planPurchased(
            $planId,
            Carbon::now()->addDays(5)->toDateTimeString()
        );

        $this->assertTrue($this->testedViewerAggregate->purchasedActivePlansIds()->contains($planId));
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldHavePlanWhichHasNotPurchased(PlanId $planId): void
    {
        $this->assertFalse($this->testedViewerAggregate->purchasedActivePlansIds()->contains($planId));
    }

    /**
     * @dataProvider planIdProvider
     */
    public function testShouldNotHavePlanAsActiveIfItExpires(PlanId $planId): void
    {
        $this->testedViewerAggregate->planPurchased(
            $planId,
            Carbon::now()->addDays(100)->toDateTimeString()
        );

        Carbon::setTestNow(Carbon::now()->addDays(100)->addMinutes(1));

        $this->assertFalse($this->testedViewerAggregate->purchasedActivePlansIds()->contains($planId));
    }

    /**
     * @dataProvider videoIdsProvider
     */
    public function testShouldTellThatHasNotPurchasedPayPerViewVideoWhenNotPurchasedIt(VideoId $videoId): void
    {
        $this->assertFalse($this->testedViewerAggregate->hasPurchasedAsPayPerView($videoId));
    }

    /**
     * @dataProvider videoIdsProvider
     */
    public function testShouldTellThatHasPurchasedPayPerViewVideoWhenPurchasedIt(VideoId ...$videoIds): void
    {
        foreach ($videoIds as $videoId) {
            $this->testedViewerAggregate->videoPayPerViewPurchased($videoId);
        }

        foreach ($videoIds as $videoId) {
            $this->assertTrue($this->testedViewerAggregate->hasPurchasedAsPayPerView($videoId));
        }
    }
}
