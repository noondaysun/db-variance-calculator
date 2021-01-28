<?php

declare(strict_types=1);

namespace Noondaysun\DbVarianceCalculator\App\Services;

use Noondaysun\DbVarianceCalculator\App\Exceptions\GitRepositoryNotFound;
use SebastianFeldmann\Git\Repository;
use ValueError;

class Git
{
    /** @var string */
    private $path;
    /** @var Repository */
    private $repository;

    /**
     * @param string $path
     * @param Repository|null $repository
     *
     * @throws GitRepositoryNotFound
     * @throws ValueError
     */
    public function __construct(string $path, Repository $repository = null)
    {
        $this->path = $path;
        $this->repository = $repository ?? new Repository($path);

        if (!is_dir(dirname($path))) {
            throw new ValueError(
                sprintf(
                    'Parameter path requires a valid path on your local system. Value supplied %s ' .
                    'could not be mapped back to a local directory',
                    $path
                )
            );
        }

        if (!Repository::isGitRepository($path)) {
            throw new GitRepositoryNotFound('Supplied path is not a valid git repository.');
        }
    }

    public function getRepositoryBranch(): array
    {
        return [
            'branch' => $this->repository->getInfoOperator()->getCurrentBranch(),
        ];
    }
}
