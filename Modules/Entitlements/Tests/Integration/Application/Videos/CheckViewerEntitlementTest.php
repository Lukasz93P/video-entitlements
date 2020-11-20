<?php

declare(strict_types=1);

namespace Modules\Entitlements\Tests\Integration\Application\Videos;

use Modules\Entitlements\Application\Videos\CheckViewerEntitlement;
use Modules\Entitlements\Domain\Videos\EntitlementCheck;
use Modules\Entitlements\Domain\Videos\Video;
use Modules\Entitlements\Domain\Videos\VideoId;
use Modules\Entitlements\Domain\Videos\VideosRepository;
use Modules\Entitlements\Domain\Viewers\Viewer;
use Modules\Entitlements\Domain\Viewers\ViewerId;
use Modules\Entitlements\Domain\Viewers\ViewersRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CheckViewerEntitlementTest extends TestCase
{
    /**
     * @var EntitlementCheck|MockObject
     */
    private MockObject $entitlementCheckMock;

    /**
     * @var MockObject|ViewersRepository
     */
    private MockObject $viewersRepository;

    /**
     * @var MockObject|VideosRepository
     */
    private MockObject $videosRepository;

    private CheckViewerEntitlement $testedCheckViewerEntitlement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entitlementCheckMock = $this->getMockBuilder(EntitlementCheck::class)->getMock();
        $this->viewersRepository = $this->getMockBuilder(ViewersRepository::class)->getMock();
        $this->videosRepository = $this->getMockBuilder(VideosRepository::class)->getMock();

        $this->testedCheckViewerEntitlement = new CheckViewerEntitlement(
            $this->entitlementCheckMock,
            $this->viewersRepository,
            $this->videosRepository
        );
    }

    public function viewerEntitlementDataWithExpectedResultProvider(): array
    {
        return [
            [ViewerId::generate(), VideoId::generate(), true],
            [ViewerId::generate(), VideoId::generate(), true],
            [ViewerId::generate(), VideoId::generate(), false],
            [ViewerId::generate(), VideoId::generate(), true],
            [ViewerId::generate(), VideoId::generate(), false],
        ];
    }

    /**
     * @dataProvider viewerEntitlementDataWithExpectedResultProvider
     */
    public function testShouldReturnResultReceivedFromEntitlementCheck(
        ViewerId $viewerId,
        VideoId $videoId,
        bool $expectedResult
    ): void {
        $viewerMock = $this->getMockBuilder(Viewer::class)->getMock();
        $this->viewersRepository->method('find')->with($viewerId)->willReturn($viewerMock);

        $videoMock = $this->getMockBuilder(Video::class)->getMock();
        $this->videosRepository
            ->method('find')
            ->with($videoId)
            ->willReturn($videoMock)
        ;

        $this->entitlementCheckMock
            ->method('isEntitledToWatch')
            ->with($videoMock, $viewerMock)
            ->willReturn($expectedResult)
        ;

        $this->assertSame(
            $expectedResult,
            $this->testedCheckViewerEntitlement->isViewerEntitledToWatchWideo(
                $viewerId->toString(),
                $videoId->toString()
            )
        );
    }
}
