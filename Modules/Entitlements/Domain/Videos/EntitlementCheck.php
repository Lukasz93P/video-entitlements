<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Videos;

use Modules\Entitlements\Domain\Viewers\Viewer;

interface EntitlementCheck
{
    public function isEntitledToWatch(Video $video, Viewer $viewer): bool;
}
