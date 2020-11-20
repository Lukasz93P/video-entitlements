<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class AggregateNotFound extends DomainException
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_NOT_FOUND;

    public static function create(): self
    {
        return new self('Requested object not found.');
    }
}
