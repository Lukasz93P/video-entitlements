<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Domain\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Domains.
 */
abstract class DomainException extends RuntimeException
{
    protected function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromReason(Throwable $reason): self
    {
        return new static($reason->getMessage(), $reason->getCode(), $reason);
    }
}
