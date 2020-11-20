<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Fakes;

use Mockery;
use Mockery\Mock;
use Modules\Entitlements\Domain\BroadcastersPlans\BroadcasterId;
use Modules\Entitlements\Domain\Videos\VideoFactory;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;

trait FakesVideosRepository
{
    private function fakeRepositoryContainsVideoWithId(VideoId $videoId, BroadcasterId $broadcasterId = null): void
    {
        $this->instance(
            VideosRepository::class,
            Mockery::mock(
                VideosRepository::class,
                /** @var Mock $mock */
                function ($mock) use ($videoId, $broadcasterId) {
                    $mock
                        ->shouldReceive('find')
                        ->andReturn(VideoFactory::create($videoId, $broadcasterId ?? BroadcasterId::generate()))
                    ;
                    $mock->shouldReceive('add');
                }
            )
        );
    }
}
