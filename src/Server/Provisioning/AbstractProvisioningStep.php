<?php

/**
 * AppserverIo\Console\Server\Provisioning\AbstractProvisioningStep
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

use AppserverIo\Provisioning\Steps\AbstractStep;
use AppserverIo\Console\Server\Services\SchemaProcessorInterface;

/**
 * An abstract step implementation that provides the base for different provisioning steps.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
abstract class AbstractProvisioningStep extends AbstractStep
{

    /**
     * The schema processor instance.
     *
     * @var \AppserverIo\Console\Server\Services\SchemaProcessorInterface
     */
    protected $schemaProcessor;

    /**
     * Initialize the provisioning step with the necessary instances.
     *
     * @param \AppserverIo\Console\Server\Services\SchemaProcessorInterface $schemaProcessor The schema processor instance
     */
    public function __construct(SchemaProcessorInterface $schemaProcessor)
    {
        $this->schemaProcessor = $schemaProcessor;
    }

    /**
     * Getter for the schema processor instance.
     *
     * @return \AppserverIo\Console\Server\Services\SchemaProcessorInterface The schema processor instance
     */
    protected function getSchemaProcessor()
    {
        return $this->schemaProcessor;
    }
}
