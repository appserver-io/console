<?php

/**
 * AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Description\MigrationDescriptor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/console
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Description;

use AppserverIo\Description\BeanDescriptor;
use AppserverIo\Lang\Reflection\ClassInterface;
use AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Annotations\Migration;

/**
 * Version bean descriptor implementation.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/routlt
 * @link       http://www.appserver.io
 */
class MigrationDescriptor extends BeanDescriptor implements MigrationDescriptorInterface
{
    /**
     * Returns a new descriptor instance.
     *
     * @return \AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Description\MigrationDescriptorInterface The descriptor instance
     */
    public static function newDescriptorInstance()
    {
        return new MigrationDescriptor();
    }

    /**
     * Returns the annotation the bean uses.
     *
     * @return string The annotation name
     */
    protected function getAnnotationName()
    {
        return Migration::ANNOTATION;
    }

    /**
     * Initializes the bean descriptor instance from the passed reflection class instance.
     *
     * @param \AppserverIo\Lang\Reflection\ClassInterface $reflectionClass The reflection class with the servlet description
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\Description\BeanDescriptorInterface|null The initialized descriptor instance
     */
    public function fromReflectionClass(ClassInterface $reflectionClass)
    {

        // add the annotation alias to the reflection class
        $reflectionClass->addAnnotationAlias(Migration::ANNOTATION, Migration::__getClass());

        // invoke the parent method
        return parent::fromReflectionClass($reflectionClass);
    }
}
