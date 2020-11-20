<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Tests\Unit\Application\Middlewares;

use Modules\SharedKernel\Application\Events\EventPublisher;
use Modules\SharedKernel\Application\Events\ExtractedEvents;
use Modules\SharedKernel\Application\Middlewares\EventsPublishingMiddleware;
use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EventsPublishingMiddlewareTest extends TestCase
{
    private EventsPublishingMiddleware $testedMiddleware;

    /**
     * @var EventPublisher|MockObject
     */
    private MockObject $eventPublisherMock;

    /**
     * @var ExtractedEvents|MockObject
     */
    private MockObject $extractedEventsMock;

    /**
     * @var Event[]
     */
    private array $eventsArray;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventPublisherMock = $this->getMockBuilder(EventPublisher::class)->getMock();
        $this->extractedEventsMock = $this
            ->getMockBuilder(ExtractedEvents::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->eventsArray = [
            $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock(),
        ];

        $this->extractedEventsMock->method('toArray')->willReturn($this->eventsArray);

        $this->testedMiddleware = new EventsPublishingMiddleware($this->eventPublisherMock);
    }

    public function valuesToReturnFromWrappedCommandProvider(): array
    {
        return [
            ['some string'],
            [234],
            [new \stdClass()],
            [[['some value'], [1, 2, 3]]],
        ];
    }

    public function testShouldPublishEventsReturnedByExtractedEventsWhichHasBeenReceivedFromWrappedCommand(): void
    {
        $this->eventPublisherMock->expects($this->once())->method('publish')->with($this->eventsArray);

        $this->testedMiddleware->handle(new \stdClass(), fn () => $this->extractedEventsMock);
    }

    public function testShouldNotPublishEventsWhenWrappedCommandHasNotReturnedExtractedEventsInstance(): void
    {
        $this->eventPublisherMock->expects($this->never())->method('publish');

        $this->testedMiddleware->handle(new \stdClass(), fn () => new \stdClass());
    }

    /**
     * @dataProvider valuesToReturnFromWrappedCommandProvider
     *
     * @param mixed $valueToReturnFromWrappedCommand
     */
    public function testShouldReturnValueReturnedByWrappedCommandWhenWrappedCommandHasNotReturnedExtractedEventsInstance(
        $valueToReturnFromWrappedCommand
    ): void {
        $returnedValue = $this->testedMiddleware->handle(new \stdClass(), fn () => $valueToReturnFromWrappedCommand);

        $this->assertSame($valueToReturnFromWrappedCommand, $returnedValue);
    }

    public function testShouldReturnValueReturnedByWrappedCommandWhenWrappedCommandHasReturnedExtractedEventsInstance(
    ): void {
        $returnedValue = $this->testedMiddleware->handle(new \stdClass(), fn () => $this->extractedEventsMock);

        $this->assertSame($this->extractedEventsMock, $returnedValue);
    }
}
