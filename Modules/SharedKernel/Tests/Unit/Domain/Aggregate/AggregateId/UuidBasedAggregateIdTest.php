<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Tests\Unit\Domain\Aggregate\AggregateId;

use Illuminate\Support\Str;
use Modules\SharedKernel\Domain\Aggregate\AggregateId\UuidBasedAggregateId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UuidBasedAggregateIdTest extends TestCase
{
    public function idStringRepresentationsProvider(): array
    {
        return [
            [Str::uuid()->toString()],
            [Str::uuid()->toString()],
            [Str::uuid()->toString()],
        ];
    }

    public function differentIdStringRepresentationsProvider(): array
    {
        return [
            [Str::uuid()->toString(), Str::uuid()->toString()],
            [Str::uuid()->toString(), Str::uuid()->toString()],
            [Str::uuid()->toString(), Str::uuid()->toString()],
            [Str::uuid()->toString(), Str::uuid()->toString()],
        ];
    }

    /**
     * @dataProvider idStringRepresentationsProvider
     */
    public function testIdsWithSameStringRepresentationShouldBeEqual(string $idStringRepresentation): void
    {
        $firstId = UuidBasedAggregateId::fromString($idStringRepresentation);
        $secondId = UuidBasedAggregateId::fromString($idStringRepresentation);

        $this->assertTrue($firstId->equals($secondId));
        $this->assertTrue($secondId->equals($firstId));
    }

    /**
     * @dataProvider differentIdStringRepresentationsProvider
     */
    public function testIdWithDifferentStringRepresentationShouldNotBeEqual(
        string $firstStringRepresentation,
        string $secondStringRepresentation
    ): void {
        $firstId = UuidBasedAggregateId::fromString($firstStringRepresentation);
        $secondId = UuidBasedAggregateId::fromString($secondStringRepresentation);

        $this->assertFalse($firstId->equals($secondId));
        $this->assertFalse($secondId->equals($firstId));
    }

    /**
     * @dataProvider idStringRepresentationsProvider
     */
    public function testShouldBeConvertibleToString(string $idStringRepresentation): void
    {
        $this->assertEquals(
            $idStringRepresentation,
            UuidBasedAggregateId::fromString($idStringRepresentation)->toString()
        );
    }

    public function testTwoGeneratedIdShouldNotBeTheSame(): void
    {
        $this->assertFalse(UuidBasedAggregateId::generate()->equals(UuidBasedAggregateId::generate()));
    }
}
