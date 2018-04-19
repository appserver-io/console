<?php

/**
 * AppserverIo\Console\Utils\DependencyInjectionKeys
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
 * A utility class for the DI service keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class DependencyInjectionKeys
{

    /**
     * The key for the application instance.
     *
     * @var string
     */
    const APPLICATION = 'application';

    /**
     * The key for the input instance.
     *
     * @var string
     */
    const INPUT = 'input';

    /**
     * The key for the output instance.
     *
     * @var string
     */
    const OUTPUT = 'output';

    /**
     * The key for the vendor directory.
     *
     * @var string
     */
    const CONFIGURATION_VENDOR_DIR = 'console.configuration.vendor.dir';
}
