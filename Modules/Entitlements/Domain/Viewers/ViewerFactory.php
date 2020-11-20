<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\Entitlements\Domain\Viewers\ViewerAggregate\ViewerAggregate;

final class ViewerFactory
{
    private function __construct()
    {
    }

    public static function create(ViewerId $id): Viewer
    {
        return ViewerAggregate::create($id);
    }
}
