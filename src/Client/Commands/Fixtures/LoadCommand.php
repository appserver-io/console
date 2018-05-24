<?php

/**
 * AppserverIo\Console\Client\Commands\Fixtures\LoadCommand
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Console\Client\Commands\Fixtures;

use Symfony\Component\Console\Command\Command;
use AppserverIo\Console\Client\Commands\CommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use AppserverIo\Console\Client\Utils\InputOptionKeys;

/**
 * The command implementation to load Doctrine Fixtures.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class LoadCommand extends Command
{

    /**
     * The trait with the basic command functionality.
     *
     * @var \AppserverIo\Console\Commands\CommandTrait
     */
    use CommandTrait;

    /**
     * The command name.
     *
     * @var string
     */
    const COMMAND = 'fixtures';

    /**
     * Configures the command's arguments and options.
     *
     * @return void
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {

        // initialize the command
        $this
            ->setName(sprintf('%s:load', $this->getCommandName()))
            ->setDescription('Load data fixtures to your database.')
            ->addArgument('fixtures', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'The directory/file to load data fixtures from.')
            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.')
            ->addOption('purge-with-truncate', null, InputOption::VALUE_NONE, 'Purge data by using a database-level TRUNCATE statement')
            ->addOption('size', null, InputOption::VALUE_OPTIONAL, 'The size of the fixtures that should be created (either "small", "medium", "large" or "huge").', 'medium')
            ->addOption(InputOptionKeys::PORT, null, InputOption::VALUE_OPTIONAL, 'The default port of the host to execute the commands on', 9023)
            ->addOption(InputOptionKeys::HOSTNAME, null, InputOption::VALUE_OPTIONAL, 'The default host name to execute the commands on', '127.0.0.1')
            ->addOption(InputOptionKeys::APPLICATION_NAME, null, InputOption::VALUE_REQUIRED, 'The application name, if the command is NOT executed from the applications root directory')
            ->setHelp(
                <<<EOT
                The <info>%command.name%</info> command loads data fixtures from your application:
                  <info>php %command.full_name%</info>
                You need to specify the DI names of the fixtures with the <info>--fixtures=MyFixtures</info> option:
                  <info>php %command.full_name% --fixtures=MyFixtures1 --fixtures=MyFixtures2</info>
                If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:
                  <info>php %command.full_name% --append</info>
                By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
                the database. If you want to use a TRUNCATE statement instead you can use the <info>--purge-with-truncate</info> flag:
                  <info>php %command.full_name% --purge-with-truncate</info>
EOT
            );
    }
}
