<?php

declare(strict_types=1);

namespace Modules\Entitlements\Domain\Viewers;

use Modules\SharedKernel\Domain\Aggregate\Events\Event;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class NewViewerRegistered extends Event
{
    public static function create(EventId $id, ViewerId $viewerId): self
    {
        return self::raise($id, $viewerId);
    }

    public function viewerId(): string
    {
        return $this->sourceId();
    }
}
