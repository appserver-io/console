<?php

/**
 * AppserverIo\Console\Server\Services\SchemaProcessorInterface
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

use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Configuration\Configuration;
use AppserverIo\Console\Server\Doctrine\Fixtures\SizeAwareFixtureInterface;

/**
 * Interface for a SFSB implementation that handles the
 * schema data for Doctrine by using Doctrine ORM itself.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
interface SchemaProcessorInterface
{

    /**
     * The name of the configuration key that contains the database name.
     *
     * @var string
     */
    const PARAM_DBNAME = 'dbname';

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
    public function createDatabase();

    /**
     * Drop the database itself.
     *
     * @return void
     */
    public function dropDatabase();

    /**
     * Tests whether our database schema needs recreation or not by comparing
     * the actual DB with our ORM entities
     *
     * @return bool
     */
    public function schemaNeedsRecreation();

    /**
     * Deletes the database schema and creates it new.
     *
     * Attention: All data will be lost if this method has been invoked.
     *
     * @return void
     */
    public function createSchema();

    /**
     * Deletes the database schema.
     *
     * Attention: All data will be lost if this method has been invoked.
     *
     * @return void
     */
    public function dropSchema();

    /**
     * Recreate's the database.
     *
     * @param boolean $checkIfNecessary Flag to invoke a check if database recreation is necessary or not
     *
     * @return void
     */
    public function recreateDatabase($checkIfNecessary = true);

    /**
     * Execute's the Doctrine migrations.
     *
     * @param \Doctrine\DBAL\Migrations\Configuration\Configuration $configuration The migration configuration
     * @param boolean                                               $dryRun        TRUE if only a dry run should be executed, else FALSE
     *
     * @return void
     */
    public function executeMigrations(Configuration $configuration, $dryRun = false);

    /**
     * Loads database fixtures
     *
     * @param string $size Size of the fixture set, either "small", "medium", "large" or "huge"
     *
     * @return void
     */
    public function loadFixtures($size = SizeAwareFixtureInterface::SMALL);
}
