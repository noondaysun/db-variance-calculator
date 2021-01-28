<?php

declare(strict_types=1);

namespace Noondaysun\DbVarianceCalculator\App\Commands;

use Noondaysun\DbVarianceCalculator\App\Exceptions\GitRepositoryNotFound;
use Noondaysun\DbVarianceCalculator\App\Services\FileDifferences;
use Noondaysun\DbVarianceCalculator\App\Services\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ValueError;

class CalculateVariances extends Command
{
    protected static $defaultName = 'app:calculate-variances';

    /**
     * Configures command description, and arguments
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription(
            'Calculates the differences between multiple database files based on git branch, and environment'
        )->addArgument(
            'git-branch',
            InputArgument::REQUIRED,
            'The file path to your local git repository'
        )->addArgument(
            'files',
            InputArgument::IS_ARRAY,
            'Space separated list of files to calculate variances for'
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gitBranch = $input->getArgument('git-branch');
        $output->writeln(
            sprintf(
                'Getting git information from %s',
                $gitBranch
            )
        );

        try {
            $git = new Git(
                $gitBranch
            );
        } catch (GitRepositoryNotFound $exception) {
            $this->showHelp($output);

            return Command::FAILURE;
        } catch (ValueError $error) {
            $this->showHelp($output);

            return Command::FAILURE;
        }

        $output->writeln('Calculating differences between files');
        (new FileDifferences($input->getArgument('files')))->calculateDifferencesAndOutput();

        return Command::SUCCESS;
    }

    private function showHelp(OutputInterface $output): void
    {
        $output->writeln(
            sprintf(
                'Usage: %s %s %s %s',
                'bin/console',
                self::$defaultName,
                '/path/to/local/git/repo',
                '/path/to/sql/file1',
                '/path/to/sql/fileN'
            )
        );
    }
}
