<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Tools\Console\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\OutputWriter;
use Symfony\Component\Console\Input\InputInterface;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Console\Server\Utils\DependencyInjectionKeys;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\XmlConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\ArrayConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\JsonConfiguration;

/**
 * The doctrine migrations command implementation.
 *
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       2.0
 * @author      Tim Wagner <tw@appserver.io>
 */
class ConfigurationHelper extends \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The DBAL connection to use.
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * The loaded configuration instance.
     *
     * @var \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Initialize the helper with the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to load the default migration configuration for
     * @param \Doctrine\DBAL\Connection                         $connection  The DBAL connection to initialize the helper with
     */

    public function __construct(ApplicationInterface $application, Connection $connection)
    {
        $this->connection = $connection;
        $this->application = $application;
    }

    /**
     * Initialize's and return's the configuration, either from the specified parameter or by loading
     * the configuration from a default file, if available.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input        The input to read arguments/parameters from
     * @param \Doctrine\DBAL\Migrations\OutputWriter          $outputWriter The output writer instance
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface The configuration instance
     */
    public function getMigrationConfig(InputInterface $input, OutputWriter $outputWriter)
    {

        // load the the configuration only once
        if ($this->configuration) {
            return $this->configuration;
        }

        // if a configuration option is passed to the command line, use that configuration instead of any other one
        if ($input->getOption('configuration')) {
            $outputWriter->write("Loading configuration from command option: " . $input->getOption('configuration'));
            return $this->configuration = $this->loadConfig($input->getOption('configuration'), $outputWriter);
        }

        //f no any other config has been found, look for default config file in the path
        $defaultConfigs = [
            'migrations.xml',
            'migrations.yml',
            'migrations.yaml',
            'migrations.json',
            'migrations.php',
        ];

        // try to locate one of the default configuration files in the application's root directory
        foreach ($defaultConfigs as $defaultConfig) {
            $config = str_replace('/', DIRECTORY_SEPARATOR, sprintf('%s/%s', $this->application->getWebappPath(), $defaultConfig));
            if ($this->configExists($config)) {
                $outputWriter->write("Loading configuration from file: $config");
                return $this->configuration = $this->loadConfig($config, $outputWriter);
            }
        }
    }

    /**
     * Load's and initializes the configuration from the passed file.
     *
     * @param string                                 $config       The name of the file to load the configuration from
     * @param \Doctrine\DBAL\Migrations\OutputWriter $outputWriter The output writer instance
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface The configuration instance
     * @throws \InvalidArgumentException Is thrown, if the given file type is not supported
     */
    public function loadConfig($config, OutputWriter $outputWriter)
    {

        // initialize a mapping from file suffix to configuration class
        $map = [
            'xml'   => XmlConfiguration::class,
            'yaml'  => YamlConfiguration::class,
            'yml'   => YamlConfiguration::class,
            'php'   => ArrayConfiguration::class,
            'json'  => JsonConfiguration::class,
        ];

        // load the file information
        $info = pathinfo($config);

        // check we can support this file type
        if (empty($map[$info['extension']])) {
            throw new \InvalidArgumentException('Given config file type is not supported');
        }

        // load the DI container instance from the application
        $container = $this->application->search(ProviderInterface::IDENTIFIER);

        // load the apropriate class name for the configuration
        $class = $map[$info['extension']];

        // initialize the configuration and add it to the DI container
        $container->set(DependencyInjectionKeys::CONFIGURATION, $configuration = new $class($this->connection, $outputWriter));

        // set the container instance and load the configuration
        $configuration->setContainer($container);
        $configuration->load($config);

        /// return the initialized configuration
        return $configuration;
    }

    /**
     * Query whether or not the file with the passed name exists.
     *
     * @param string $config The file to query for
     *
     * @return boolean TRUE if the file exists, else FALSE
     */
    protected function configExists($config)
    {
        return file_exists($config);
    }
}
