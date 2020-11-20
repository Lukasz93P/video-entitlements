<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Tests\Fakes\FakesViewersRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ViewerPayPerViewVideoPurchaseRegistrationFeatureTest extends TestCase
{
    use FakesViewersRepository;

    public function viewerAndVideoIdsProvide(): array
    {
        return [
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
            [ViewerId::generate(), VideoId::generate()],
        ];
    }

    /**
     * @dataProvider viewerAndVideoIdsProvide
     */
    public function testShouldAcceptViewerPayPerViewVideoPurchaseRegistrationRequest(
        ViewerId $viewerId,
        VideoId $videoId
    ): void {
        $this->fakeRepositoryContainsViewerWithId($viewerId);

        $response = $this->put(
            route(
                'viewers.videos.purchased',
                ['viewerId' => $viewerId->toString()]
            ),
            ['videoId' => $videoId->toString()]
        );

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
