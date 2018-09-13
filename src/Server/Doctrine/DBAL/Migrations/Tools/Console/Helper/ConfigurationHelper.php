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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Console\Server\Utils\DependencyInjectionKeys;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\XmlConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\YamlConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\ArrayConfiguration;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\JsonConfiguration;
use AppserverIo\Properties\PropertiesUtil;

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
     * The array with the default configuration filenames.
     *
     * @var array
     */
    protected $defaultConfigs = array(
        'migrations.xml',
        'migrations.yml',
        'migrations.yaml',
        'migrations.json',
        'migrations.php',
    );

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
        if ($config = $input->getOption('configuration')) {
            // query whether or not the configuration file exits
            if ($this->configExists($absolutePath = $this->getAbsolutePath($config))) {
                $outputWriter->write("Loading configuration from file: " . $absolutePath);
                return $this->configuration = $this->loadConfig($absolutePath, $outputWriter);
            }

            // throw an exception if the given configuration file is NOT available
            throw new InvalidConfigurationException(
                sprintf(
                    'Can\'t read configuration from file "%s"',
                    $input->getOption('configuration')
                )
            );
        }

        // if no any other config has been found, look for default config file in the path
        $defaultConfigs = $this->getDefaultConfigs();

        // try to locate one of the default configuration files in the application's root directory
        foreach ($defaultConfigs as $defaultConfig) {
            // query whether or not the configuration file exits
            if ($this->configExists($absolutePath = $this->getAbsolutePath($defaultConfig))) {
                $outputWriter->write("Loading default configuration from file: $absolutePath");
                return $this->configuration = $this->loadConfig($absolutePath, $outputWriter);
            }
        }

        // throw an exception no a configuration file has been specified nor a default file is available
        throw new InvalidConfigurationException(
            sprintf(
                'Can\'t find any default configuration file (one of "%s"',
                implode(', ', $this->getDefaultConfigs())
            )
        );
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

        // replace the system properies first
        $config = $this->replaceSystemProperties($config);

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

        // return the initialized configuration
        return $configuration;
    }

    /**
     * Replaces the variables with the application's system properties and write
     * the content to a temporary file in the application's data directory.
     *
     * @param string $config The configuration file with the variables that has to be replaced
     *
     * @return string The path of the temporary file with the content
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException Is thrown, if the temporary file can't be written
     */
    protected function replaceSystemProperties($config)
    {

        // replace the variablies in the configuration file
        $content = $this->getApplication()->replaceSystemProperties($config);

        // create name for the cached version of the file
        $cachedConfig = sprintf('%s/%s', $this->getApplication()->getCacheDir(), basename($config));

        // write the configuration file with the replaced variables to the cache directory
        if (file_put_contents($cachedConfig, $content)) {
            return $cachedConfig;
        }

        // throw an exception, if the cached configuration file can not we written
        throw new InvalidConfigurationException(sprintf('Can\'t write cached configuration for configuration file "%s"', $config));
    }

    /**
     * Returns the absolute path for the passed confiuration file.
     *
     * @param string $config The relative path of the configuration file
     *
     * @return string|null The absolute path of the configuration file
     */
    protected function getAbsolutePath($config)
    {

        // query whether or not we already have an absolute path
        if (is_file($config)) {
            return $config;
        }

        // perpare the path, concatenate it with the web application directory
        $preparedPath = str_replace('/', DIRECTORY_SEPARATOR, sprintf('%s/%s', $this->getApplication()->getWebappPath(), $config));

        // return the concatenated path, if the file is available
        if (is_file($preparedPath)) {
            return $preparedPath;
        }
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
        return file_exists($config) && is_readable($config);
    }

    /**
     * Return's the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the filenames for the default configuration files.
     *
     * @return array The filenames
     */
    protected function getDefaultConfigs()
    {
        return $this->defaultConfigs;
    }
}
