<?php

/**
 * AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Description\MigrationDescriptorInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/console
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Description;

use AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface;

/**
 * Interface for version descriptor implementations.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
interface MigrationDescriptorInterface extends BeanDescriptorInterface
{
}
