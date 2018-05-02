<?php

/**
 * AppserverIo\Console\Server\Doctrine\Fixtures\SizeAwareFixtureInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Console\Server\Doctrine\Fixtures;

/**
 * The interface for fixture with a variable size.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
interface SizeAwareFixtureInterface
{

    /**
     * The name of a "small" fixture set.
     *
     * @var string
     */
    const SMALL = 'small';

    /**
     * The name of a "medium" fixture set.
     *
     * @var string
     */
    const MEDIUM = 'medium';

    /**
     * The name of a "large" fixture set.
     *
     * @var string
     */
    const LARGE = 'large';

    /**
     * The name of a "huge" fixture set.
     *
     * @var string
     */
    const HUGE = 'huge';

    /**
     * Setter for the fixture set's size to load.
     *
     * @param string $size Size of the fixture set, either "small", "medium", "large" or "huge"
     *
     * @return void
     */
    public function setSize($size);

    /**
     * Getter for the fixture set's size to load.
     *
     * @return string Size of the fixture set, either "small", "medium", "large" or "huge"
     */
    public function getSize();
}
