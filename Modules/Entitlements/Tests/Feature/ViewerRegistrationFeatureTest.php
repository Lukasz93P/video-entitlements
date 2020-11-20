<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ViewerRegistrationFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function broadcasterIdProvider(): array
    {
        return [
            [ViewerId::generate()],
            [ViewerId::generate()],
            [ViewerId::generate()],
        ];
    }

    /**
     * @dataProvider broadcasterIdProvider
     */
    public function testShouldAcceptViewerRegisterRequest(ViewerId $viewerId): void
    {
        $response = $this->postJson(route('viewers.create'), ['id' => $viewerId->toString()]);

        $response->assertStatus(Response::HTTP_ACCEPTED);
    }
}
