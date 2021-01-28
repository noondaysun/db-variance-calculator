<?php

declare(strict_types=1);

namespace Noondaysun\DbVarianceCalculator\App\Services;

use DiffMatchPatch\DiffMatchPatch;
use League\Flysystem\Filesystem;

class FileDifferences
{
    /** @var DiffMatchPatch */
    private $diffMatchPatch;
    /** @var array */
    private $files;
    /** @var Filesystem */
    private $fileSystem;

    /**
     * @param array $files
     * @param Filesystem|null $fileSystem
     * @param DiffMatchPatch|null $diffMatchPatch
     */
    public function __construct(array $files, ?Filesystem $fileSystem = null, ?DiffMatchPatch $diffMatchPatch = null)
    {
        $this->files = $files;
        $this->fileSystem = $fileSystem;
        $this->diffMatchPatch = $diffMatchPatch;
    }

    public function calculateDifferencesAndOutput(): void
    {
        foreach ($this->files as $fileHandle) {
            $this->fileSystem->readStream($fileHandle);
        }
    }
}
