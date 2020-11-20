<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Unit\Domain\Videos;

use Modules\Resources\Domain\Videos\Title;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TitleTest extends TestCase
{
    public function videoTitlesProvider(): array
    {
        return [
            ['some title', 'another title'],
            ['video 1', '2 video 2'],
            ['my test video', 'some other test video'],
        ];
    }

    /**
     * @dataProvider videoTitlesProvider
     */
    public function testShouldBeEqualWhenCreatedWithSameTitle(string $title): void
    {
        $firstTitle = Title::fromString($title);
        $secondTitle = Title::fromString($title);

        $this->assertTrue($firstTitle->equals($secondTitle));
        $this->assertTrue($secondTitle->equals($firstTitle));
    }

    /**
     * @dataProvider videoTitlesProvider
     */
    public function testShouldNotBeEqualWhenCreateWithDifferentTitles(string $title, string $differentTitle): void
    {
        $firstTitle = Title::fromString($title);
        $secondTitle = Title::fromString($differentTitle);

        $this->assertFalse($firstTitle->equals($secondTitle));
        $this->assertFalse($secondTitle->equals($firstTitle));
    }
}
