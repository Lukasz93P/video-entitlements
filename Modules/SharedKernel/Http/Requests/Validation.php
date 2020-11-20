<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Http\Requests;

class Validation
{
    public const UUID_REGEX_MATCH = '[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}';
}
