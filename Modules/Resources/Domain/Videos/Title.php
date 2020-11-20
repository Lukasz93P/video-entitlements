<?php

declare(strict_types=1);

namespace Modules\Resources\Domain\Videos;

class Title
{
    private string $title;

    private function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function fromString(string $title): self
    {
        return new self($title);
    }

    public function equals(Title $other): bool
    {
        return $this->title === $other->title;
    }

    public function toString(): string
    {
        return $this->title;
    }
}
