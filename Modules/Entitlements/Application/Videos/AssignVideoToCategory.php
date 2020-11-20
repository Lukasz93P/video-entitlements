<?php

declare(strict_types=1);

namespace Modules\Entitlements\Application\Videos;

use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\Video;

class AssignVideoToCategory extends ChangeVideoCategory
{
    protected function changeVideoCategory(Video $video, CategoryId $categoryId): void
    {
        $video->assignToCategory($categoryId);
    }
}
