<?php

declare(strict_types=1);

namespace Modules\SharedKernel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\SharedKernel\Domain\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *    title="Entitlements API",
 *    version="1.0.0",
 * )
 */
class BaseController extends Controller
{
    protected function executeLoginReturningProperResponse(
        callable $logic,
        int $successResponseCode = Response::HTTP_OK,
        array $responseHeaders = []
    ): JsonResponse {
        try {
            $logic();

            return response()->json([], $successResponseCode, $responseHeaders);
        } catch (DomainException $domainException) {
            return response()->json(
                ['message' => $domainException->getMessage()],
                $domainException->getCode() ?: Response::HTTP_BAD_REQUEST,
                $responseHeaders
            );
        }
    }
}
