<?php

declare(strict_types=1);

namespace Modules\Resources\Tests\Unit\Domain\Videos\VideoAggregate;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Videos\Title;
use Modules\Resources\Domain\Videos\VideoAggregate\VideoAggregate;
use Modules\Resources\Domain\Videos\VideoId;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VideoAggregateTest extends TestCase
{
    public function videoNamesProvider(): array
    {
        return [
            [Title::fromString('test'), Title::fromString('some test')],
            [Title::fromString('some video'), Title::fromString('testing videos')],
            [Title::fromString('123 test 321'), Title::fromString('recruitment video')],
        ];
    }

    /**
     * @dataProvider videoNamesProvider
     */
    public function testShouldHasProvidedName(Title $name): void
    {
        $videoAggregate = VideoAggregate::create(VideoId::generate(), BroadcasterId::generate(), $name);

        $this->assertTrue($name->equals($videoAggregate->getTitle()));
    }

    /**
     * @dataProvider videoNamesProvider
     */
    public function testShouldChangeName(Title $oldName, Title $newName): void
    {
        $videoAggregate = VideoAggregate::create(VideoId::generate(), BroadcasterId::generate(), $oldName);

        $videoAggregate->changeTitle($newName);

        $this->assertTrue($newName->equals($videoAggregate->getTitle()));
    }
}
