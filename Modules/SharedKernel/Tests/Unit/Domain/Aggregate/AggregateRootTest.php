<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Tests\Unit\Domain\Aggregate;

use Illuminate\Support\Str;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\AggregateId;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AggregateRootTest extends TestCase
{
    /**
     * @var AggregateId|MockObject
     */
    private MockObject $aggregateIdMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aggregateIdMock = $this->getMockBuilder(AggregateId::class)->getMock();
    }

    public function aggregateIdStringRepresentationProvider(): array
    {
        return [
            [Str::uuid()->toString()],
            [Str::uuid()->toString()],
            [Str::uuid()->toString()],
        ];
    }

    /**
     * @dataProvider aggregateIdStringRepresentationProvider
     */
    public function testShouldHasProperId(string $idStringRepresentation): void
    {
        $this->aggregateIdMock->method('toString')->willReturn($idStringRepresentation);

        $testBaseAggregateRoot = $this->createTestAggregateInstance($this->aggregateIdMock);

        $this->assertEquals($idStringRepresentation, $testBaseAggregateRoot->id()->toString());
    }

    private function createTestAggregateInstance(AggregateId $id): AggregateRoot
    {
        return new class($id) extends AggregateRoot {
            public function __construct(AggregateId $id)
            {
                parent::__construct($id);
            }
        };
    }
}
