<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\BroadcastersPlans;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Application\BroadcastersPlans\AttachPlanToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\BroadcastersPlans\ChildCannotBeAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanAttachedToParent;
use Modules\Entitlements\Domain\BroadcastersPlans\PlanId;
use Modules\Entitlements\Tests\Fakes\FakesBroadcastersPlansRepository;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AttachChildToParentTest extends TestCase
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
    public function testShouldAttachChildToParent(PlanId $childId, PlanId $parentId, BroadcasterId $broadcasterId): void
    {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $childId, $parentId);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        Event::assertDispatched(
            PlanAttachedToParent::class,
            static fn (PlanAttachedToParent $event) => $event->broadcasterId() === $broadcasterId->toString()
                && $event->childPlanId() === $childId->toString() && $event->parentPlanId() === $parentId->toString()
        );
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldNotAllowToAttachSameChildForSameParentMoreThanOnce(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $childId, $parentId);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));

        $this->expectException(ChildCannotBeAttachedToParent::class);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldNotAllowToAttachNonExistingChildToParent(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $parentId);

        $this->expectException(ChildCannotBeAttachedToParent::class);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));
    }

    /**
     * @dataProvider childPlanParentPlanAndBroadcasterIdsProvider
     */
    public function testShouldNotAllowToAttachChildToNonExistingParent(
        PlanId $childId,
        PlanId $parentId,
        BroadcasterId $broadcasterId
    ): void {
        $this->fakeRepositoryContainsBroadcasterPlansWithPredefinedPlansWithin($broadcasterId, $parentId);

        $this->expectException(ChildCannotBeAttachedToParent::class);

        dispatch_now(new AttachPlanToParent($broadcasterId->toString(), $childId->toString(), $parentId->toString()));
    }
}
