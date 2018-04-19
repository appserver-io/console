<?php

/**
 * AppserverIo\Console\Commands\Migrations\UpToDateCommand
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

namespace AppserverIo\Console\Commands\Migrations;

use AppserverIo\Console\Commands\CommandTrait;

/**
 * The command implementation to tell you if your schema is up-to-date.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class UpToDateCommand extends \Doctrine\DBAL\Migrations\Tools\Console\Command\UpToDateCommand
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
    const COMMAND = 'migrations';
}
