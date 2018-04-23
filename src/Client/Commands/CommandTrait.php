<?php

/**
 * AppserverIo\Console\Client\Commands\CommandTrait
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

namespace AppserverIo\Console\Client\Commands;

use Psr\Log\LoggerInterface;
use AppserverIo\Console\Client\Utils\InputOptionKeys;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait implementation that provides basic telnet remote command invocation functionality.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
trait CommandTrait
{

    /**
     * The logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Initializes the command with the passed logger instance.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger instance
     */
    public function __construct(LoggerInterface $logger)
    {

        // set the logger instance
        $this->logger = $logger;

        // you *must* call the parent constructor
        parent::__construct();
    }

    /**
     * Return's the logger instance.
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * Return's the command name (overrides the original command name).
     *
     * @return string The command name
     */
    protected function getCommandName()
    {
        return self::COMMAND;
    }

    /**
     * Return's the application name to use which can either be the one that has been passed
     * as commandline option 'application-name' or the name of the actual folder.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input the input stream
     *
     * @return string The application name
     */
    protected function getApplicationName(InputInterface $input)
    {

        // query whether or not an application name has been specified
        if ($input->getOption(InputOptionKeys::APPLICATION_NAME)) {
            return $input->getOption(InputOptionKeys::APPLICATION_NAME);
        }

        // return the name of the acutal folder as application name
        return basename(getcwd());
    }

    /**
     * Configures the command.
     *
     * @return void
     */
    protected function configure()
    {

        // invoke the parent method
        parent::configure();

        // add the client specific options that will NOT be passed through to the remote host
        $this->addOption(InputOptionKeys::PORT, null, InputOption::VALUE_OPTIONAL, 'The default port of the host to execute the commands on', 9023);
        $this->addOption(InputOptionKeys::HOSTNAME, null, InputOption::VALUE_OPTIONAL, 'The default host name to execute the commands on', '127.0.0.1');
        $this->addOption(InputOptionKeys::APPLICATION_NAME, null, InputOption::VALUE_REQUIRED, 'The application name, if the command is NOT executed from the applications root directory');
    }

    /**
     * Executes the command's functionality on the remote host.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  The input stream
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output stream
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        // initialize the DSV to connect to
        $dsn = sprintf('%s:%d', $input->getOption(InputOptionKeys::HOSTNAME), $input->getOption(InputOptionKeys::PORT));

        // initialize the options that has to be passed
        $options = array();

        // prepare the options we want to pass through
        foreach ($input->getOptions() as $name => $value) {
            // do NOT pass client options
            if (InputOptionKeys::isOption($name)) {
                continue;
            }
            // append real command options
            if ($value) {
                $options[] = "--$name=$value";
            }
        }

        // initialize the command itself
        $command = sprintf('AppserverIo\Console\Server\Commands\ConsoleCommand %s %s %s', $this->getApplicationName($input), $this->getCommandName(), $this->getName());

        // append the options that has to be passed
        if ($opts = trim(implode(' ', $options))) {
            $command .= sprintf(' %s', $opts);
        }

        // append the arguments that has to be passed
        if ($args = trim(str_replace($this->getName(), null, implode(' ', $input->getArguments())))) {
            $command .= sprintf(' "%s"', $args);
        }

        // log the command that'll be executed
        $this->getLogger()->info(sprintf('Now execute command [%s]: %s', $dsn, $command));

        // initialize the telnet client, connect and execute the command on the remot host
        $client = \Graze\TelnetClient\TelnetClient::factory();
        $client->connect($dsn);
        $response = $client->execute($command);

        // write the response to the console
        $output->write("{$response->getResponseText()}\n");
    }
}
