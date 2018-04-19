<?php

/**
 * AppserverIo\Console\Utils\InputOptionKeys
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

namespace AppserverIo\Console\Utils;

/**
 * A utility class for the input option keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class InputOptionKeys
{

    /**
     * The key for the input option 'hostname'.
     *
     * @var string
     */
    const HOSTNAME = 'hostname';

    /**
     * The key for the input option 'port'.
     *
     * @var string
     */
    const PORT = 'port';

    /**
     * The key for the input option 'application-name'.
     *
     * @var string
     */
    const APPLICATION_NAME = 'application-name';

    /**
     * Return's the available option keys as array.
     *
     * @return array The available option keys
     */
    public static function getOptionKeys()
    {
        return array(
            InputOptionKeys::APPLICATION_NAME,
            InputOptionKeys::HOSTNAME,
            InputOptionKeys::PORT
        );
    }

    /**
     * Queries whether or not the passed option key is a valid input option key or not.
     *
     * @param string $optionKey The option key to query for
     *
     * @return boolean TRUE if the passed option key is valid, else FALSE
     */
    public static function isOption($optionKey)
    {
        return in_array($optionKey, InputOptionKeys::getOptionKeys());
    }
}
