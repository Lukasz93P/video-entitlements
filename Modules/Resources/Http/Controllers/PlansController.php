<?php

declare(strict_types=1);

namespace Modules\Resources\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Resources\Application\Plans\AddNewPlan;
use Modules\Resources\ReadModel\PlansReadModel;
use Modules\SharedKernel\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class PlansController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/resources/broadcasters/{broadcasterId}/plans",
     * summary="Add new plan",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster",
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
    public function addNewPlan(string $broadcasterId): JsonResponse
    {
        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId) {
                dispatch(new AddNewPlan($broadcasterId));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Get(
     * path="/api/resources/broadcasters/{broadcasterId}/plans",
     * summary="Get broadcasters plans",
     * @OA\Parameter(
     *   name="broadcasterId",
     *   in="path",
     *   required=true,
     *   description="Id of broadcaster",
     *   @OA\Schema(
     *     type="string",
     *     format="uuid",
     *     example="e4c7180e-602d-43d1-b7a9-a0a77c0d49a2"
     *   )
     * ),
     * @OA\Response(
     *    response=200,
     *     description="Broadcastesrs plans ids",
     *    @OA\JsonContent(
     *       @OA\Property(property="plansIds", type="array",
     *          @OA\Items(type="string", format="uuid")
     *        )
     *     )
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
    public function getBroadcastersPlans(string $broadcasterId, PlansReadModel $plansReadModel): JsonResponse
    {
        return response()->json(['plansIds' => $plansReadModel->getBroadcastersPlansIds($broadcasterId)]);
    }
}
