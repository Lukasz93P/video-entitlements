<?php

declare(strict_types=1);

namespace Modules\Resources\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Resources\Application\Videos\AddNewVideo;
use Modules\Resources\ReadModel\VideosReadModel;
use Modules\SharedKernel\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;

class VideosController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/resources/broadcasters/{broadcasterId}/videos",
     * summary="Add new video",
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
     * @OA\RequestBody(
     *    required=true,
     *    description="Data required to add new video",
     *    @OA\JsonContent(
     *       required={"title"},
     *       @OA\Property(
     *          property="title",
     *          type="string",
     *          example="Some video title",
     *          description="Title of added video"
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
    public function addNewVideo(string $broadcasterId, Request $request): JsonResponse
    {
        $request->validate(['title' => 'required']);

        return $this->executeLoginReturningProperResponse(
            function () use ($broadcasterId, $request) {
                dispatch(new AddNewVideo($broadcasterId, $request->get('title')));
            },
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * @OA\Get(
     * path="/api/resources/broadcasters/{broadcasterId}/videos",
     * summary="Get broadcasters videos",
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
     *     description="Broadcasters videos ids",
     *    @OA\JsonContent(
     *       @OA\Property(property="videosIds", type="array",
     *          @OA\Items(type="string", format="uuid")
     *       )
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
    public function getBroadcastersVideos(string $broadcasterId, VideosReadModel $videosReadModel): JsonResponse
    {
        return response()->json(['videosIds' => $videosReadModel->getBroadcastersVideosIds($broadcasterId)]);
    }
}
