<?php

/**
 * AppserverIo\Console\Server\Provisioning\CreateDatabaseStep
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

/**
 * A step implementation that creates the database, if necessary.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class CreateDatabaseStep extends AbstractProvisioningStep
{

    /**
     * Does the actual database creation.
     *
     * @return void
     *
     * @throws \Exception On provisioning failure
     */
    public function execute()
    {
        $this->getSchemaProcessor()->createDatabase();
    }
}
