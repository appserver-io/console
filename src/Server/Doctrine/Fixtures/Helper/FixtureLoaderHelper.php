<?php

/**
 * AppserverIo\Console\Server\Doctrine\Fixtures\Helper\FixtureLoaderHelper
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

namespace AppserverIo\Console\Server\Doctrine\Fixtures\Helper;

use Symfony\Component\Console\Helper\Helper;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * Helper implementation that returns the application's entity manager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class FixtureLoaderHelper extends Helper
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the helper with the actual application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Return's the application intance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Retrieves the default Doctrine ORM EntityManager.
     *
     * @return \Doctrine\ORM\EntityManagerInterface The entity manager instance
     * @throws \Exception Is thrown, if not default entity manager is available
     */
    public function getFixtureLoader()
    {
        return $this->getApplication()->search('FixtureLoader');
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     * @see \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper::getName()
     */
    public function getName()
    {
        return 'fixtureLoader';
    }
}
