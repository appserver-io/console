<?php

/**
 * AppserverIo\Console\Server\Api\Node\DoctrineMigrationsNode
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
 * The implementation of the doctrine migrations configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class DoctrineMigrationsNode extends AbstractNode
{

    /**
     * The migration's name.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @DI\Mapping(nodeName="name", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $name;

    /**
     * The migration's namespace.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @DI\Mapping(nodeName="migrations-namespace", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $migrationsNamespace;

    /**
     * The migration's directory.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @DI\Mapping(nodeName="migrations-directory", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $migrationsDirectory;

    /**
     * The migration's table.
     *
     * @var \AppserverIo\Console\Server\Api\Node\TableNode
     * @DI\Mapping(nodeName="table", nodeType="AppserverIo\Console\Server\Api\Node\TableNode")
     */
    protected $table;

    /**
     * Returns the migration's name.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The migration's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the migration's namespace.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The migration's namespace
     */
    public function getMigrationsNamespace()
    {
        return $this->migrationsNamespace;
    }

    /**
     * Returns the migration's directory.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The migration's directory
     */
    public function getMigrationsDirectory()
    {
        return $this->migrationsDirectory;
    }

    /**
     * Returns the migration's name.
     *
     * @return \AppserverIo\Console\Server\Api\Node\TableNode The migration's name
     */
    public function getTable()
    {
        return $this->table;
    }
}
