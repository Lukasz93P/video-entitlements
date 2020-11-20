<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos\VideoAggregate;

use Modules\Resources\Domain\Broadcasters\BroadcasterId;
use Modules\Resources\Domain\Videos\NewVideoAdded;
use Modules\Resources\Domain\Videos\Title;
use Modules\Resources\Domain\Videos\Video;
use Modules\Resources\Domain\Videos\VideoId;
use Modules\Resources\Domain\Videos\VideoTitleChanged;
use Modules\SharedKernel\Domain\Aggregate\AggregateRoot;
use Modules\SharedKernel\Domain\Aggregate\Events\EventId;

class VideoAggregate extends AggregateRoot implements Video
{
    private BroadcasterId $broadcasterId;

    private Title $title;

    private function __construct(VideoId $id, BroadcasterId $broadcasterId, Title $title)
    {
        parent::__construct($id);

        $this->broadcasterId = $broadcasterId;
        $this->title = $title;
    }

    public static function create(VideoId $id, BroadcasterId $broadcasterId, Title $title): self
    {
        $newInstance = new self($id, $broadcasterId, $title);

        $newInstance->registerRaisedEvent(NewVideoAdded::create(EventId::generate(), $id, $broadcasterId, $title));

        return $newInstance;
    }

    public function changeTitle(Title $newTitle): void
    {
        if ($this->title->equals($newTitle)) {
            return;
        }

        $this->title = $newTitle;

        $this->registerRaisedEvent(
            VideoTitleChanged::create(EventId::generate(), $this->id(), $this->broadcasterId(), $this->getTitle())
        );
    }

    public function broadcasterId(): BroadcasterId
    {
        return $this->broadcasterId;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }
}
