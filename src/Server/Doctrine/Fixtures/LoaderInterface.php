<?php

/**
 * AppserverIo\Console\Server\Doctrine\Fixtures\LoaderInterface
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

/**
 * The interface for fixture loader implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
interface LoaderInterface
{

    /**
     * Find's and load's fixtures with the given names.
     *
     * @param array $fixtureNames The DI names of the fixture classes
     *
     * @return array $fixtures Array of loaded fixture object instances
     */
    public function loadFromNames(array $fixtureNames);

    /**
     * Return's all available fixture instances.
     *
     * @return array The array with the fixture instances
     */
    public function loadAll();
}
