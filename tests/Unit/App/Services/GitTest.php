<?php

declare(strict_types=1);

namespace Noondaysun\DbVarianceCalculator\Tests\Unit\App\Services;

use Noondaysun\DbVarianceCalculator\App\Exceptions\GitRepositoryNotFound;
use Noondaysun\DbVarianceCalculator\App\Services\Git;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\Operator\Info;
use SebastianFeldmann\Git\Repository;

class GitTest extends TestCase
{
    /** @var Repository */
    private $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(Repository::class);
    }

    public function testFailIfPassedInvalidPath(): void
    {
        $this->expectErrorMessageMatches(
            '/^Parameter path requires a valid path on your local system. Value supplied .*' .
            ' could not be mapped back to a local directory$/'
        );

        $git = new Git('/test/test.file', $this->repository);
    }

    public function testFailIfPassedInvalidGitDirectory(): void
    {
        $this->expectException(GitRepositoryNotFound::class);

        $git = new Git('/tmp');
    }

    public function testGetRepositoryBranch(): void
    {
        $infoOperator = $this->createMock(Info::class);
        $infoOperator->expects($this->once())
            ->method('getCurrentBranch')
            ->willReturn('main');

        $this->repository->expects($this->once())
            ->method('getInfoOperator')
            ->willReturn($infoOperator);

        $pathParts = explode('/', __DIR__);
        $chunks = array_chunk($pathParts, array_search('db-variance-calculator', $pathParts) + 1);

        $git = new Git(implode('/', $chunks[0]), $this->repository);

        $this->assertEquals(
            [
                'branch' => 'main',
            ],
            $git->getRepositoryBranch()
        );
    }
}
