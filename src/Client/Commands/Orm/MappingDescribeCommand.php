<?php

/**
 * AppserverIo\Console\Client\Commands\Orm\ValidateSchemaCommand
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

namespace AppserverIo\Console\Client\Commands\Orm;

use AppserverIo\Console\Client\Commands\CommandTrait;
use AppserverIo\Console\Client\Utils\InputOptionKeys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * The command implementation to show information about mapped entities.
 *
 * We can NOT extend the original class, because it is FINAL!!
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class MappingDescribeCommand extends Command
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
    const COMMAND = 'orm';

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('orm:mapping:describe')
            ->addArgument('entityName', InputArgument::REQUIRED, 'Full or partial name of entity')
            ->setDescription('Display information about mapped objects')
            ->setHelp(
                <<<EOT
The %command.full_name% command describes the metadata for the given full or partial entity class name.

    <info>%command.full_name%</info> My\Namespace\Entity\MyEntity

Or:

    <info>%command.full_name%</info> MyEntity
EOT
            );

        // add the client specific options that will NOT be passed through to the remote host
        $this->addOption(InputOptionKeys::PORT, null, InputOption::VALUE_OPTIONAL, 'The default port of the host to execute the commands on', 9023);
        $this->addOption(InputOptionKeys::HOSTNAME, null, InputOption::VALUE_OPTIONAL, 'The default host name to execute the commands on', '127.0.0.1');
        $this->addOption(InputOptionKeys::APPLICATION_NAME, null, InputOption::VALUE_REQUIRED, 'The application name, if the command is NOT executed from the applications root directory');

        // invoke the parent method
        parent::configure();
    }
}
