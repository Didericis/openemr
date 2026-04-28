<?php

/**
 * Console command that loads a slice of representative sample data
 * (facilities, users with assorted ACL groups, patients, encounters) for
 * local development. Re-running cleans previously-seeded rows first, so it is
 * idempotent.
 *
 *  php bin/console openemr:seed-dev-data --site=default
 *  php bin/console openemr:seed-dev-data --clean
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

namespace OpenEMR\Common\Command;

use OpenEMR\Tools\DevelopmentSeed\DevelopmentSeeder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SeedDevelopmentDataCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('openemr:seed-dev-data')
            ->setDescription('Seed sample development data (facilities, users with ACL groups, patients, encounters).')
            ->addUsage('--site=default')
            ->addUsage('--clean')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Name of site', 'default')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Remove previously-seeded development data and exit.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seeder = new DevelopmentSeeder($output);

        if ($input->getOption('clean')) {
            $seeder->clean();
            $output->writeln('<info>Development seed data cleaned.</info>');
            return Command::SUCCESS;
        }

        $seeder->seed();
        $output->writeln('<info>Development seed data installed.</info>');
        $output->writeln('Default password for all seeded users: <comment>pass</comment>');
        return Command::SUCCESS;
    }
}
