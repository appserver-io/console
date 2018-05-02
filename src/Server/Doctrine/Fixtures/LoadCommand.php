<?php

/**
 * AppserverIo\Console\Server\Doctrine\Fixtures\LoadCommand
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

namespace AppserverIo\Console\Server\Doctrine\Fixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppserverIo\Console\Server\Doctrine\Fixtures\Purger\ORMPurger;
use AppserverIo\Console\Server\Doctrine\Fixtures\Executor\ORMExecutor;

/**
 * The command implementation to load Doctrine Fixtures.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class LoadCommand extends \AppserverIo\Console\Client\Commands\Fixtures\LoadCommand
{


    /**
     * Initializes the command with the passed logger instance.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger instance
     */
    public function __construct()
    {
        // you *must* call the parent constructor
        Command::__construct();
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \InvalidArgumentException When this abstract method is not implemented
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        // load the helper for the fixture loader to use
        /** @var \AppserverIo\Console\Server\Doctrine\Fixtures\Helper\FixtureLoaderHelper $fixtureLoaderHelper */
        $fixtureLoaderHelper = $this->getHelper('fixtureLoader');

        // try to load the fixtures specified on the commandline
        $loader = $fixtureLoaderHelper->getFixtureLoader();

        // load the directory with the fixtures or a fixture file itself
        if ($fixtureNames = $input->getArgument('fixtures')) {
            // make sure we've an array with fixture names and try to load the fixtures by their names
            $loader->loadFromNames(is_array($fixtureNames) ? $fixtureNames : array($fixtureNames), $input->getOption('size'));
        } else {
            $loader->loadAll($input->getOption('size'));
        }

        // query whether or not we've initialize fixture instance
        if ($fixtures = $loader->getFixtures()) {
            // load the helper for the entity manager to use
            /** @var \AppserverIo\Console\Server\Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper $entityManagerHelper */
            $entityManagerHelper = $this->getHelper('em');

            // load the helper for the application to use
            /** @var \AppserverIo\Console\Server\Doctrine\Fixtures\Helper\ApplicationHelper $applicationHelper */
            $applicationHelper = $this->getHelper('application');

            // load the application and the entity manager
            $application = $applicationHelper->getApplication();
            $entityManager = $entityManagerHelper->getEntityManager();

            // initialize the ORM purter and executor instances
            $purger = new ORMPurger($application, $entityManager, $fixtures);
            $purger->setPurgeMode($input->getOption('purge-with-truncate') ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
            $executor = new ORMExecutor($entityManager, $purger);

            // initialize the output logger of the executor
            $executor->setLogger(function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            });

            // invoke the executor and return
            $executor->execute($fixtures, $input->getOption('append'));
            return;
        }

        // throw an exception if no fixtures are available
        throw new \InvalidArgumentException(
            sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
        );
    }
}
