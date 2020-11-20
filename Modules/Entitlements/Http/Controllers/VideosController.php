<?php

declare(strict_types=1);

namespace Modules\Entitlements\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Entitlements\Application\Videos\AssignVideoToCategory;
use Modules\Entitlements\Application\Videos\AssignVideoToPlan;
use Modules\Entitlements\Application\Videos\CheckViewerEntitlement;
use Modules\Entitlements\Application\Videos\UnassignVideoFromCategory;
use Modules\Entitlements\Application\Videos\UnassignVideoFromPlan;
use Modules\SharedKernel\Domain\Exceptions\DomainException;
use Modules\SharedKernel\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class VideosController extends BaseController
{
    /**
     * @OA\Put(
     * path="/api/entitlements/videos/{videoId}/plans",
     * summary="Assign video to plan",
     * @OA\Parameter(
     *   name="videoId",
     *   in="path",
     *   required=true,
     *   description="Id of video which should be assigned to plan",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"planId"},
     *       @OA\Property(
     *          property="planId",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of plan to which video should be assigned",
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
    public function assignVideoToPlan(string $videoId, Request $request): JsonResponse
    {
        $request->validate(['planId' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($videoId, $request) {
                dispatch(new AssignVideoToPlan($videoId, $request->get('planId')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Delete(
     * path="/api/entitlements/videos/{videoId}/plans/{planId}",
     * summary="Unassign video from plan",
     * @OA\Parameter(
     *   name="videoId",
     *   in="path",
     *   required=true,
     *   description="Id of video which should be unassigned from plan",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Parameter(
     *   name="planId",
     *   in="path",
     *   required=true,
     *   description="Id of plan from which video should be unassigned",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
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
    public function unassignVideoToPlan(string $videoId, string $planId): JsonResponse
    {
        return $this->executeLoginReturningProperResponse(
            function () use ($videoId, $planId) {
                dispatch(new UnassignVideoFromPlan($videoId, $planId));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Put(
     * path="/api/entitlements/videos/{videoId}/categories",
     * summary="Assign video to category",
     * @OA\Parameter(
     *   name="videoId",
     *   in="path",
     *   required=true,
     *   description="Id of video which should be assigned to category",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"categoryId"},
     *       @OA\Property(
     *          property="categoryId",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of category to which video should be assigned",
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
    public function assignVideoToCategory(string $videoId, Request $request): JsonResponse
    {
        $request->validate(['categoryId' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($videoId, $request) {
                dispatch(new AssignVideoToCategory($videoId, $request->get('categoryId')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Delete(
     * path="/api/entitlements/videos/{videoId}/categories/{categoryId}",
     * summary="Unassign video from category",
     * @OA\Parameter(
     *   name="videoId",
     *   in="path",
     *   required=true,
     *   description="Id of video which should be unassigned from category",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Parameter(
     *   name="categoryId",
     *   in="path",
     *   required=true,
     *   description="Id of category from which video should be unassigned",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
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
    public function unassignVideoFromCategory(string $videoId, string $categoryId): JsonResponse
    {
        return $this->executeLoginReturningProperResponse(
            function () use ($videoId, $categoryId) {
                dispatch(new UnassignVideoFromCategory($videoId, $categoryId));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Get(
     * path="/api/entitlements/videos/{videoId}/viewers/{viewerId}",
     * summary="Check if viewer is entitled to watch the video",
     * @OA\Parameter(
     *   name="videoId",
     *   in="path",
     *   required=true,
     *   description="Id of video to which entitlement should be checked",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Parameter(
     *   name="viewerId",
     *   in="path",
     *   required=true,
     *   description="Id of viewer for which entitlement should be checked",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Viewer entitled to watch video",
     *    @OA\MediaType(
     *         mediaType="application/json",
     *    )
     * ),
     * @OA\Response(
     *    response=403,
     *    description="Forbidden, viewer not entitled to watch video"
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
    public function isViewerEntitledToWatchVideo(
        string $videoId,
        string $viewerId,
        CheckViewerEntitlement $checkViewerEntitlement
    ): JsonResponse {
        try {
            $isUserEntitledToWatchVideo = $checkViewerEntitlement->isViewerEntitledToWatchWideo($viewerId, $videoId);

            return response()->json([], $isUserEntitledToWatchVideo ? Response::HTTP_OK : Response::HTTP_FORBIDDEN);
        } catch (DomainException $domainException) {
            return response()->json(
                ['message' => $domainException->getMessage()],
                $domainException->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }
    }
}
