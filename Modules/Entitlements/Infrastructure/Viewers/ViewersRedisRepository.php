<?php

declare(strict_types=1);

namespace Modules\Entitlements\Infrastructure\Viewers;

use Illuminate\Support\Facades\Redis;
use Modules\Entitlements\Domain\Viewers\Viewer;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use Modules\SharedKernel\Domain\Exceptions\AggregateNotFound;

class ViewersRedisRepository implements ViewersRepository
{
    private const KEY_SPACE = 'entitlements_viewers';

    public function add(Viewer $viewer): void
    {
        Redis::set($this->buildKeyForViewer($viewer->id()), serialize($viewer));
    }

    public function find(ViewerId $id): Viewer
    {
        $result = Redis::get($this->buildKeyForViewer($id));

        if (!$result) {
            throw AggregateNotFound::create();
        }

        return unserialize($result, [Viewer::class]);
    }

    private function buildKeyForViewer(ViewerId $viewerId): string
    {
        return self::KEY_SPACE."_{$viewerId->toString()}";
    }
}
