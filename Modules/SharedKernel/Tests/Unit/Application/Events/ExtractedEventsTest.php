<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Tests\Unit\Application\Events;

use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Domain\Aggregate\Aggregate;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ExtractedEventsTest extends TestCase
{
    /**
     * @var Aggregate|MockObject
     */
    private MockObject $aggregateMock;

    /**
     * @var Aggregate|MockObject
     */
    private MockObject $secondAggregateMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aggregateMock = $this->getMockBuilder(Aggregate::class)->getMock();
        $this->secondAggregateMock = $this->getMockBuilder(Aggregate::class)->getMock();
    }

    public function testShouldReturnEventsWhichWasHeldByProvidedAggregate(): void
    {
        $event = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $secondEvent = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();

        $this->aggregateMock->method('raisedEvents')->willReturn([$event]);
        $this->secondAggregateMock->method('raisedEvents')->willReturn([$secondEvent]);

        $extractedEvents = ExtractedEvents::extractEventsFrom(
            $this->aggregateMock,
            $this->secondAggregateMock
        )->toArray();

        $this->assertContains($event, $extractedEvents);
        $this->assertContains($secondEvent, $extractedEvents);
        $this->assertCount(2, $extractedEvents);
    }

    public function testShouldClearAggregateEvents(): void
    {
        $this->aggregateMock->expects($this->once())->method('clearRaisedEvents');
        $this->secondAggregateMock->expects($this->once())->method('clearRaisedEvents');

        ExtractedEvents::extractEventsFrom($this->aggregateMock, $this->secondAggregateMock)->toArray();
    }

    public function testShouldReturnEmptyArrayWhenProvidedAggregatesHasNoRaisedEvents(): void
    {
        $extractedEvents = ExtractedEvents::extractEventsFrom(
            $this->aggregateMock,
            $this->secondAggregateMock
        )->toArray();

        $this->assertEmpty($extractedEvents);
    }
}
