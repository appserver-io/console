<?php

/**
 * AppserverIo\Console\Server\Utils\ParamKeys
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

namespace AppserverIo\Console\Server\Utils;

/**
 * Utility class with provisioning configuration keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class ParamKeys
{

    /**
     * The configuration key for the fixtures size.
     *
     * @var string
     */
    const SIZE = 'size';

    /**
     * The configuration key for a flag to check if database recreation is necessary.
     *
     * @var string
     */
    const CHECK_IF_NECESSARY = 'checkIfNecessary';

    /**
     * The configuration key for a migration's dry run flag.
     *
     * @var string
     */
    const DRY_RUN = 'dryRun';

    /**
     * The configuration key for a migration's deployment descriptor.
     *
     * @var string
     */
    const DEPLOYMENT_DESCRIPTOR = 'deploymentDescriptor';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}
