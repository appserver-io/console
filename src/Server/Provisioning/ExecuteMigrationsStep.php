<?php

/**
 * AppserverIo\Console\Server\Provisioning\ExecuteMigrationsStep
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

namespace AppserverIo\Console\Server\Provisioning;

use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Console\Server\Utils\ParamKeys;
use AppserverIo\Console\Server\Api\Node\DoctrineMigrationsNode;
use AppserverIo\Console\Server\Services\SchemaProcessorInterface;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\Configuration;

/**
 * A step implementation that executes the migrations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class ExecuteMigrationsStep extends AbstractProvisioningStep
{

    /**
     * The DI provider instance.
     *
     * @var \AppserverIo\Psr\Di\ProviderInterface
     */
    protected $provider;

    /**
     * Initialize the provisioning step with the necessary instances.
     *
     * @param \AppserverIo\Psr\Di\ProviderInterface                         $provider        The DI provider instance
     * @param \AppserverIo\Console\Server\Services\SchemaProcessorInterface $schemaProcessor The schema processor instance
     */
    public function __construct(ProviderInterface $provider, SchemaProcessorInterface $schemaProcessor)
    {

        // set the provider instance
        $this->provider = $provider;

        // pass the schema processor to the parent instance
        parent::__construct($schemaProcessor);
    }

    /**
     * Return's the application's DI provider instance.
     *
     * @return \Psr\Container\ContainerInterface The DI provider instance
     */
    protected function getProvider()
    {
        return $this->provider;
    }

    /**
     * Return's the migration configuration.
     *
     * @return \AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    protected function getConfiguration()
    {
        return $this->getApplication()->search((new ReflectionClass(Configuration::class))->getShortName());
    }

    /**
     * Does the actual provisioning.
     *
     * @return void
     *
     * @throws \Exception On provisioning failure
     */
    public function execute()
    {

        // load the migration configuration parameter
        $dryRun = $this->getParam(ParamKeys::DRY_RUN);
        $deploymentDescriptor = $this->getParam(ParamKeys::DEPLOYMENT_DESCRIPTOR);

        // load the migration configuration and replace the properties
        $node = new DoctrineMigrationsNode();
        $node->initFromFile($deploymentDescriptor);

        // load the migration configuration and replace the properties
        $node->replaceProperties($this->getSystemProperties());

        // load the configuration instance
        $configuration = $this->getConfiguration();

        // initialize the migrations configuration
        $configuration->setContainer($this->getProvider());
        $configuration->setMigrationsTableName($node->getTable()->getName());
        $configuration->setMigrationsNamespace($node->getMigrationsNamespace());
        $configuration->setMigrationsDirectory($migrationsDirectory = $node->getMigrationsDirectory());

        // create the migration table, it not already exists
        $configuration->createMigrationTable();

        // register the migrations found in the migration directory
        $configuration->registerMigrationsFromDirectory($migrationsDirectory);

        // load the migrations configuration and execute the migrations
        $this->getSchemaProcessor()->executeMigrations($configuration, $dryRun);
    }
}
