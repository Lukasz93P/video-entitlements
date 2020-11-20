<?php

declare(strict_types=1);

namespace Modules\Entitlements\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Entitlements\Application\Viewer\RegisterNewViewer;
use Modules\Entitlements\Application\Viewer\RegisterThatViewerPurchasedPayPerViewVideo;
use Modules\Entitlements\Application\Viewer\RegisterThatViewerPurchasedPlan;
use Modules\SharedKernel\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ViewersController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/entitlements/viewers",
     * summary="Register new viewer",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"id"},
     *       @OA\Property(
     *          property="id",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of viewer which should be registered"
     *     ),
     *    ),
     * ),
     * @OA\Response(
     *    response=202,
     *     description="Accepted",
     *    @OA\MediaType(
     *         mediaType="application/json",
     *    )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Error description")
     *        )
     *     )
     * )
     */
    public function registerViewer(Request $request): JsonResponse
    {
        $request->validate(['id' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($request) {
                dispatch(new RegisterNewViewer($request->get('id')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Put(
     * path="/api/entitlements/viewers/{viewerId}/plans",
     * summary="Register that viewer purchased a plan",
     * @OA\Parameter(
     *   name="viewerId",
     *   in="path",
     *   required=true,
     *   description="Id of viewer who purchased a plan",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"planId", "expiresAt"},
     *       @OA\Property(
     *          property="planId",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of purchased plan",
     *      ),
     *      @OA\Property(
     *          property="expiresAt",
     *          type="string",
     *          format="datetime",
     *          example="2021-01-01 00:00:00",
     *          description="Moment of plan expiration",
     *      ),
     *    ),
     * ),
     * @OA\Response(
     *    response=202,
     *     description="Accepted",
     *    @OA\MediaType(
     *         mediaType="application/json",
     *    )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Error description")
     *        )
     *     )
     * )
     */
    public function registerViewerPurchasedPlan(string $viewerId, Request $request): JsonResponse
    {
        $request->validate(['planId' => 'uuid|required', 'expiresAt' => 'date_format:Y-m-d H:i:s|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($viewerId, $request) {
                dispatch(
                    new RegisterThatViewerPurchasedPlan($viewerId, $request->get('planId'), $request->get('expiresAt'))
                );
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Put(
     * path="/api/entitlements/viewers/{viewerId}/videos",
     * summary="Register that viewer purchased a pay per view video",
     * @OA\Parameter(
     *   name="viewerId",
     *   in="path",
     *   required=true,
     *   description="Id of viewer who purchased a video",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"videoId"},
     *       @OA\Property(
     *          property="videoId",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of purchased video",
     *      )
     *    ),
     * ),
     * @OA\Response(
     *    response=202,
     *     description="Accepted",
     *    @OA\MediaType(
     *         mediaType="application/json",
     *    )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Error description")
     *        )
     *     )
     * )
     */
    public function registerViewerPurchasedPayPerViewVideo(string $viewerId, Request $request): JsonResponse
    {
        $request->validate(['videoId' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($viewerId, $request) {
                dispatch(
                    new RegisterThatViewerPurchasedPayPerViewVideo($viewerId, $request->get('videoId'))
                );
            },
            Response::HTTP_ACCEPTED
        );
    }
}
