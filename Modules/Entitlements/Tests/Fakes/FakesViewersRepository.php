<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Fakes;

use Mockery;
use Mockery\Mock;
use Modules\Entitlements\Domain\Viewers\ViewerFactory;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;

trait FakesViewersRepository
{
    private function fakeRepositoryContainsViewerWithId(ViewerId $viewerId): void
    {
        $this->instance(
            ViewersRepository::class,
            Mockery::mock(
                ViewersRepository::class,
                /** @var Mock $mock */
                function ($mock) use ($viewerId) {
                    $mock
                        ->shouldReceive('find')
                        ->andReturn(ViewerFactory::create($viewerId))
                    ;
                    $mock->shouldReceive('add');
                }
            )
        );
    }
}
