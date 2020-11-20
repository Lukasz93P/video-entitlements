<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\BroadcastersPlans;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\BroadcastersPlans\AttachPlanToParent;
use Modules\Entitlements\Application\BroadcastersPlans\DetachPlanFromParent;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeDetachedFromParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanDetachedFromParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DetachChildFromParentTest extends TestCase
{
    use FakesBroadcastersPlansRepository;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function childPlanParentPlanAndBroadcasterIdsProvider(): array
    {
        return [
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
            [PlanId::generate(), PlanId::generate(), BroadcasterId::generate()],
        ];
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldDetachChildFromParent(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $childId, $parentId);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        dispatch_now(new DetachPlanFromParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        Event::assertDispatched(
            PlanDetachedFromParent::class,
            static fn (PlanDetachedFromParent $event) => $event->broadcasterId() === $broadcasterId->toString()
                && $event->childPlanId() === $childId->toString() && $event->parentPlanId() === $parentId->toString()
        );
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldNotAllowToDetachSameChildFromSameParentMoreThanOnce(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $childId, $parentId);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        dispatch_now(new DetachPlanFromParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        $this->expectException(ChildCannotBeDetachedFromParent::class);

        dispatch_now(new DetachPlanFromParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldNotAllowToDetachNotExistingChildFromParent(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $childId, $parentId);

        $this->expectException(ChildCannotBeDetachedFromParent::class);

        dispatch_now(new DetachPlanFromParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));
    }
}
