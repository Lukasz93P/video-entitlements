<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\Categories\CategoryId;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Tests\Fakes\FakesVideosRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class VideosCategoriesManagementFeatureTest extends TestCase
{
    use FakesVideosRepository;

    public function videoAndCategoryIdsProvider(): array
    {
        return [
            [VideoId::generate(), CategoryId::generate()],
            [VideoId::generate(), CategoryId::generate()],
            [VideoId::generate(), CategoryId::generate()],
        ];
    }

    /**
     * @dataProvider videoAndCategoryIdsProvider
     */
    public function testShouldAcceptAssignVideoToCategoryRequest(VideoId $videoId, CategoryId $categoryId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoId);

        $response = $this->put(
            route(
                'videos.categories.assign',
                ['videoId' => $videoId->toString()]
            ),
            ['categoryId' => $categoryId->toString()]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }

    /**
     * @dataProvider videoAndCategoryIdsProvider
     */
    public function testShouldAcceptUnassignVideoToCategoryRequest(VideoId $videoId, CategoryId $categoryId): void
    {
        $this->fakeRepositoryContainsVideoWithId($videoId);

        $response = $this->delete(
            route(
                'videos.categories.unassign',
                ['videoId' => $videoId->toString(), 'categoryId' => $categoryId->toString()]
            ),
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
