<?php

/**
 * AppserverIo\Console\Server\Api\Node\TableNode
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

namespace AppserverIo\Console\Server\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * The implementation of the doctrine migrations table configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2018 TechDivision GmbH <info@techdivision.com>
 * @link      https://www.techdivision.com
 */
class TableNode extends AbstractNode
{

    /**
     * The migration's table name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * Returns the migration's table name.
     *
     * @return string The migration's table name
     */
    public function getName()
    {
        return $this->name;
    }
}
