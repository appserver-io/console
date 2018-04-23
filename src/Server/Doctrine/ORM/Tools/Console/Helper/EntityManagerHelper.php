<?php

/**
 * AppserverIo\Console\Server\Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper
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

namespace AppserverIo\Console\Server\Doctrine\ORM\Tools\Console\Helper;

/**
 * A utility class for the DI service keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class EntityManagerHelper extends \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper
{

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     * @see \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper::getName()
     */
    public function getName()
    {
        return 'em';
    }
}
