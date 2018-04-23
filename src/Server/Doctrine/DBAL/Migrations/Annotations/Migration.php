<?php

/**
 * AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Annotations\Migration
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

namespace AppserverIo\Console\Server\Doctrine\DBAL\Migrations\Annotations;

use AppserverIo\Psr\EnterpriseBeans\Annotations\AbstractBeanAnnotation;

/**
 * Annotation to map a Doctrine Migration version.
 *
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2015 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/console
 * @link       http://www.appserver.io
 */
class Migration extends AbstractBeanAnnotation
{

    /**
     * The annotation to define Doctrine Migration version.
     *
     * @var string
     */
    const ANNOTATION = 'Migration';

    /**
     * This method returns the class name as
     * a string.
     *
     * @return string
     */
    public static function __getClass()
    {
        return __CLASS__;
    }

    /**
     * Returns the value of the name attribute.
     *
     * @return string|null The annotations name attribute
     */
    public function getName()
    {
        if (isset($this->values['name'])) {
            return $this->values['name'];
        }
    }
}
