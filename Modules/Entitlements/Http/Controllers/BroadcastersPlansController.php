<?php

declare(strict_types=1);

namespace Modules\Entitlements\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Entitlements\Application\BroadcastersPlans\AssignCategoryToPlan;
use Modules\Entitlements\Application\BroadcastersPlans\AttachPlanToParent;
use Modules\Entitlements\Application\BroadcastersPlans\DetachPlanFromParent;
use Modules\Entitlements\Application\BroadcastersPlans\RegisterBroadcaster;
use Modules\Entitlements\Application\BroadcastersPlans\UnassignCategoryFromPlan;
use Modules\SharedKernel\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class BroadcastersPlansController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/entitlements/broadcasters",
     * summary="Register new broadcaster",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"id"},
     *       @OA\Property(
     *          property="id",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of broadcaster which should be registered"
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
    public function registerBroadcaster(Request $request): JsonResponse
    {
        $request->validate(['id' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($request) {
                dispatch(new RegisterBroadcaster($request->get('id')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Put(
     * path="/api/entitlements/broadcasters/{broadcasterId}/plans/{planId}/parents",
     * summary="Attach plan to parent",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster which owns plan",
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
     *   description="Id of plan which should be attached to parent",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"parentPlanId"},
     *       @OA\Property(
     *          property="parentPlanId",
     *          type="string",
     *          format="uuid",
     *          example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2",
     *          description="Id of desired parent plan",
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
    public function attachPlanToParent(string $broadcasterId, string $planId, Request $request): JsonResponse
    {
        $request->validate(['parentPlanId' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId, $planId, $request) {
                dispatch(new AttachPlanToParent($broadcasterId, $planId, $request->get('parentPlanId')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Delete(
     * path="/api/entitlements/broadcasters/{broadcasterId}/plans/{planId}/parents/{parentPlanId}",
     * summary="Detach plan from parent",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster which owns plan",
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
     *   description="Id of plan which should be detached from parent",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Parameter(
     *   name="parentPlanId",
     *   in="path",
     *   required=true,
     *   description="Id of parent plan from which plan should be detached",
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
    public function detachPlanFromParent(string $broadcasterId, string $planId, string $parentPlanId): JsonResponse
    {
        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId, $planId, $parentPlanId) {
                dispatch(new DetachPlanFromParent($broadcasterId, $planId, $parentPlanId));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Put(
     * path="/api/entitlements/broadcasters/{broadcasterId}/plans/{planId}/categories",
     * summary="Assign category to plan",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster which owns plan",
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
     *   description="Id of plan to which category should be assigned",
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
     *          description="Id of category which should be assigned to plan",
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
    public function assignCategoryToPlan(string $broadcasterId, string $planId, Request $request): JsonResponse
    {
        $request->validate(['categoryId' => 'uuid|required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId, $planId, $request) {
                dispatch(new AssignCategoryToPlan($broadcasterId, $planId, $request->get('categoryId')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Delete(
     * path="/api/entitlements/broadcasters/{broadcasterId}/plans/{planId}/categories/{categoryId}",
     * summary="Unassign category from plan",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster which owns plan",
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
     *   description="Id of plan from which category should be unassigned",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     *  @OA\Parameter(
     *   name="categoryId",
     *   in="path",
     *   required=true,
     *   description="Id of category which should be unassigned from plan",
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
    public function unassignCategoryFromPlan(string $broadcasterId, string $planId, string $categoryId): JsonResponse
    {
        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId, $planId, $categoryId) {
                dispatch(new UnassignCategoryFromPlan($broadcasterId, $planId, $categoryId));
            },
            Response::HTTP_ACCEPTED
        );
    }
}
