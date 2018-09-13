<?php

/**
 * AppserverIo\Console\Server\Services\SchemaProcessor
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

namespace AppserverIo\Console\Server\Services;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\SqliteSchemaManager;
use AppserverIo\Console\Server\Doctrine\Fixtures\Purger\ORMPurger;
use AppserverIo\Console\Server\Doctrine\Fixtures\Executor\ORMExecutor;
use AppserverIo\Console\Server\Doctrine\Fixtures\SizeAwareFixtureInterface;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\Configuration;

/**
 * A singleton session bean implementation that handles the
 * schema data for Doctrine by using Doctrine ORM itself.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
trait SchemaProcessorTrait
{

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The initialized Doctrine entity manager
     */
    abstract protected function getApplication();

    /**
     * Returns the DI provider instance.
     *
     * @return \AppserverIo\Psr\Di\ProviderInterface The DI provider instance
     */
    abstract protected function getProvider();

    /**
     * Returns the object manager instance.
     *
     * @return \AppserverIo\Psr\Di\ObjectManagerInterface The object manager instance
     */
    abstract protected function getObjectManager();

    /**
     * Returns the timer service context instance.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerServiceContextInterface The timer service context instance
     */
    abstract protected function getTimerServiceContext();

    /**
     * Returns the initialized Doctrine entity manager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface The initialized Doctrine entity manager
     */
    abstract protected function getEntityManager();

    /**
     * Creates the database itself.
     *
     * This quite seems to be a bit strange, because with all databases
     * other than SQLite, we need to remove the database name from the
     * connection parameters BEFORE connecting to the database, as
     * connection to a not existing database fails.
     *
     * @return void
     */
    public function createDatabase()
    {

        try {
            // clone the connection and load the database name
            $connection = clone $this->getEntityManager()->getConnection();
            $dbname = $connection->getDatabase();
            // remove the the database name
            $params = $connection->getParams();
            if (isset($params[SchemaProcessorInterface::PARAM_DBNAME])) {
                unset($params[SchemaProcessorInterface::PARAM_DBNAME]);
            }
            // create a new connection WITHOUT the database name
            $cn = DriverManager::getConnection($params);
            $sm = $cn->getDriver()->getSchemaManager($cn);
            // SQLite doesn't support database creation by a method
            if ($sm instanceof SqliteSchemaManager) {
                return;
            }
            // query whether or not the database already exists
            if (!in_array($dbname, $sm->listDatabases())) {
                $sm->createDatabase($dbname);
            }
        } catch (\Exception $e) {
            \error($e);
        }
    }

    /**
     * Drop the database itself.
     *
     * @return void
     */
    public function dropDatabase()
    {

        try {
            // clone the connection and load the database name
            $connection = clone $this->getEntityManager()->getConnection();
            $dbname = $connection->getDatabase();
            // remove the the database name
            $params = $connection->getParams();
            if (isset($params[SchemaProcessorInterface::PARAM_DBNAME])) {
                unset($params[SchemaProcessorInterface::PARAM_DBNAME]);
            }
            // create a new connection WITHOUT the database name
            $cn = DriverManager::getConnection($params);
            $sm = $cn->getDriver()->getSchemaManager($cn);
            // SQLite doesn't support database creation by a method
            if ($sm instanceof SqliteSchemaManager) {
                return;
            }
            // query whether or not the database already exists
            if (in_array($dbname, $sm->listDatabases())) {
                $sm->dropDatabase($dbname);
            }
        } catch (\Exception $e) {
            \error($e);
        }
    }

    /**
     * Tests whether our database schema needs recreation or not by comparing
     * the actual DB with our ORM entities
     *
     * @return bool
     */
    public function schemaNeedsRecreation()
    {

        // load the entity manager and the schema tools
        $entityManager = $this->getEntityManager();
        $schemaTool = new SchemaTool($entityManager);
        $schemaManager = $entityManager->getConnection()->getSchemaManager();
        $comparator = new Comparator();

        // load the class definitions
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        // load the two schemas
        $dbSchema = $schemaManager->createSchema();
        $entitySchema = $schemaTool->getSchemaFromMetadata($classes);

        // we also need the platform
        $platform = $entityManager->getConnection()->getDatabasePlatform();

        // initialize the array for the DB diffs
        $diffSql = [];

        // we have to compare two ways, as deleting columns within the entities might fail our comparison otherwise
        try {
            $schemaDiff = $comparator->compare($dbSchema, $entitySchema);
            $diffSql = $schemaDiff->toSql($platform);
        } catch (\Exception $e) {
            $schemaDiff = $comparator->compare($entitySchema, $dbSchema);
            $diffSql = $schemaDiff->toSql($platform);
        }

        // return TRUE if the diff is empty, else FALSE
        return sizeof($diffSql) === 0;
    }

    /**
     * Deletes the database schema and creates it new.
     *
     * Attention: All data will be lost if this method has been invoked.
     *
     * @return void
     */
    public function createSchema()
    {

        try {
            // load the entity manager and the schema tool
            $entityManager = $this->getEntityManager();
            $schemaTool = new SchemaTool($entityManager);

            // load the class definitions
            $classes = $entityManager->getMetadataFactory()->getAllMetadata();

            // create or update the schema
            $schemaTool->updateSchema($classes);
        } catch (\Exception $e) {
            \error($e);
        }
    }

    /**
     * Deletes the database schema.
     *
     * Attention: All data will be lost if this method has been invoked.
     *
     * @return void
     */
    public function dropSchema()
    {

        // load the entity manager and the schema tool
        $entityManager = $this->getEntityManager();
        $schemaTool = new SchemaTool($entityManager);

        // load the class definitions
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        // drop the schema if it already exists and create it new
        $schemaTool->dropSchema($classes);
    }

    /**
     * Recreate's the database.
     *
     * @param boolean $checkIfNecessary Flag to invoke a check if database recreation is necessary or not
     *
     * @return void
     */
    public function recreateDatabase($checkIfNecessary = true)
    {

        // query whether or not the schema needs to be re-created
        if (($this->schemaNeedsRecreation() && $checkIfNecessary) || !$checkIfNecessary) {
            // log a message
            \info('Now start to re-create schema');
            // drop and create the database
            $this->dropDatabase();
            $this->createDatabase();
        }
    }

    /**
     * Execute's the Doctrine migrations.
     *
     * @param \Doctrine\DBAL\Migrations\Configuration\Configuration $configuration The migration configuration
     * @param boolean                                               $dryRun        TRUE if only a dry run should be executed, else FALSE
     *
     * @return void
     */
    public function executeMigrations(Configuration $configuration, $dryRun = false)
    {

        // create the migration instance
        $migration = new \AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Migration($configuration);

        // run the migrations
        $migration->migrate(null, $dryRun);
    }

    /**
     * Loads database fixtures
     *
     * @param string $size Size of the fixture set, either "small", "medium", "large" or "huge"
     *
     * @return void
     */
    public function loadFixtures($size = SizeAwareFixtureInterface::SMALL)
    {

        // load the fixtures loader
        /** \AppserverIo\Console\Server\Doctrine\Fixtures\LoaderInterface $loader */
        $loader = $this->getProvider()->get('FixtureLoader');

        // load the fixture sets with the passed size
        $loader->loadAll($size);

        // load the application and entity manager to use
        $application = $this->getApplication();
        $entityManager = $this->getEntityManager();

        // load the fixtures
        $fixtures = $loader->getFixtures();

        // get an executor and load the given fixtures
        $purger = new ORMPurger($application, $entityManager, $fixtures);
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($fixtures);
    }
}
