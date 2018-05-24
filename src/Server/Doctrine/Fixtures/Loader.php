<?php

/**
 * AppserverIo\Console\Server\Doctrine\Fixtures\Loader
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

namespace AppserverIo\Console\Server\Doctrine\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\Exception\CircularReferenceException;
use AppserverIo\Psr\Di\ProviderInterface;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Description\FixtureDescriptorInterface;

/**
 * A class that loads Doctrine fixtures.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/console
 * @link      http://www.appserver.io
 */
class Loader implements LoaderInterface
{

    /**
     * Array of fixture object instances to execute.
     *
     * @var array
     */
    private $fixtures = array();

    /**
     * Array of ordered fixture object instances.
     *
     * @var array
     */
    private $orderedFixtures = array();

    /**
     * Determines if we must order fixtures by number
     *
     * @var boolean
     */
    private $orderFixturesByNumber = false;

    /**
     * Determines if we must order fixtures by its dependencies
     *
     * @var boolean
     */
    private $orderFixturesByDependencies = false;

    /**
     * The file extension of fixture files.
     *
     * @var string
     */
    private $fileExtension = '.php';

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the loader with the DI container instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Return's the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Return's the application's DI container instance.
     *
     * @return \Psr\Container\ContainerInterface The DI container instance
     */
    protected function getContainer()
    {
        return $this->getApplication()->search(ProviderInterface::IDENTIFIER);
    }

    /**
     * Return's the application's object manager istance.
     *
     * @return \AppserverIo\Psr\Di\ObjectManagerInterface The object manager instance
     */
    protected function getObjectManager()
    {
        return $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);
    }

    /**
     * Find's and load's fixtures with the given names.
     *
     * @param array  $fixtureNames The DI names of the fixture classes
     * @param string $size         The fixture size to load
     *
     * @return array $fixtures Array of loaded fixture object instances
     */
    public function loadFromNames(array $fixtureNames, $size = SizeAwareFixtureInterface::SMALL)
    {
        return $this->loadFromIteratorWithSize(new \ArrayIterator($fixtureNames), $size);
    }

    /**
     * Return's all available fixture instances.
     *
     * @param string $size The fixture size to load
     *
     * @return array The array with the fixture instances
     */
    public function loadAll($size = SizeAwareFixtureInterface::SMALL)
    {

        // initialize the array for the DI names of the fixtures
        $fixtureNames = array();

        // load the object descriptors and query whether or not if they implment the fixture descriptor interface
        foreach ($this->getObjectManager()->getObjectDescriptors() as $objectDescriptor) {
            if ($objectDescriptor instanceof FixtureDescriptorInterface) {
                $fixtureNames[] = $objectDescriptor->getName();
            }
        }

        // load and return the fixture instance
        return $this->loadFromIteratorWithSize(new \ArrayIterator($fixtureNames), $size);
    }

    /**
     * Load fixtures from files contained in iterator.
     *
     * @param \Iterator $iterator Iterator over files from which fixtures should be loaded
     * @param string    $size     The fixture size to load
     *
     * @return array $fixtures Array of loaded fixture object instances
     */
    protected function loadFromIteratorWithSize(\Iterator $iterator, $size)
    {

        // initialize the array for the fixtures
        $fixtures = array();

        // iterate over the possible fixture class names
        foreach ($iterator as $lookupName) {
            // try to load the fixture by the lookup name
            $possibleFixture = $this->getContainer()->get($lookupName);

            // query whether or not we've found a fixture
            if ($this->isTransient(get_class($possibleFixture))) {
                continue;
            }

            // set the size if we've size aware fixture
            if ($possibleFixture instanceof SizeAwareFixtureInterface) {
                $possibleFixture->setSize($size);
            }

            // if we've found a fixture, add it to the loader
            $fixtures[] = $possibleFixture;
            $this->addFixture($possibleFixture, $lookupName);
        }

        // return the array with the initialized fixtures
        return $fixtures;
    }

    /**
     * Has fixture?
     *
     * @param FixtureInterface $fixture
     *
     * @return boolean
     */
    public function hasFixture($fixture)
    {
        return isset($this->fixtures[get_class($fixture)]);
    }

    /**
     * Get a specific fixture instance
     *
     * @param string $className
     * @return FixtureInterface
     */
    public function getFixture($className)
    {

        if (isset($this->fixtures[$className])) {
            return $this->fixtures[$className];
        }

        throw new \InvalidArgumentException(sprintf('"%s" is not a registered fixture', $className));
    }

    /**
     * Add a fixture object instance to the loader.
     *
     * @param \Doctrine\Common\DataFixtures\FixtureInterface $fixture    The fixture instance
     * @param string                                         $lookupName The DI lookup name of the fixture to add
     *
     * @return void
     */
    public function addFixture(FixtureInterface $fixture, $lookupName)
    {

        $fixtureClass = get_class($fixture);

        if (isset($this->fixtures[$fixtureClass])) {
            return;
        }

        if ($fixture instanceof OrderedFixtureInterface && $fixture instanceof DependentFixtureInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Class "%s" can\'t implement "%s" and "%s" at the same time.',
                    get_class($fixture),
                    'OrderedFixtureInterface',
                    'DependentFixtureInterface'
                )
            );
        }

        $this->fixtures[$fixtureClass] = $fixture;

        if ($fixture instanceof OrderedFixtureInterface) {
            $this->orderFixturesByNumber = true;
        } elseif ($fixture instanceof DependentFixtureInterface) {
            $this->orderFixturesByDependencies = true;
            foreach($fixture->getDependencies() as $dependencyLookupName) {
                if ($this->getContainer()->has($dependencyLookupName)) {
                    $this->addFixture($this->getContainer()->get($dependencyLookupName), $dependencyLookupName);
                }
            }
        }
    }

    /**
     * Returns the array of data fixtures to execute.
     *
     * @return array $fixtures
     */
    public function getFixtures()
    {
        $this->orderedFixtures = array();

        if ($this->orderFixturesByNumber) {
            $this->orderFixturesByNumber();
        }

        if ($this->orderFixturesByDependencies) {
            $this->orderFixturesByDependencies();
        }

        if (!$this->orderFixturesByNumber && !$this->orderFixturesByDependencies) {
            $this->orderedFixtures = $this->fixtures;
        }

        return $this->orderedFixtures;
    }

    /**
     * Check if a given fixture is transient and should not be considered a data fixtures
     * class.
     *
     * @return boolean
     */
    public function isTransient($className)
    {
        $rc = new \ReflectionClass($className);
        if ($rc->isAbstract()) return true;

        $interfaces = class_implements($className);
        return in_array('Doctrine\Common\DataFixtures\FixtureInterface', $interfaces) ? false : true;
    }

    /**
     * Orders fixtures by number
     *
     * @todo maybe there is a better way to handle reordering
     * @return void
     */
    private function orderFixturesByNumber()
    {
        $this->orderedFixtures = $this->fixtures;
        usort($this->orderedFixtures, function($a, $b) {
            if ($a instanceof OrderedFixtureInterface && $b instanceof OrderedFixtureInterface) {
                if ($a->getOrder() === $b->getOrder()) {
                    return 0;
                }
                return $a->getOrder() < $b->getOrder() ? -1 : 1;
            } elseif ($a instanceof OrderedFixtureInterface) {
                return $a->getOrder() === 0 ? 0 : 1;
            } elseif ($b instanceof OrderedFixtureInterface) {
                return $b->getOrder() === 0 ? 0 : -1;
            }
            return 0;
        });
    }


    /**
     * Orders fixtures by dependencies
     *
     * @return void
     */
    private function orderFixturesByDependencies()
    {
        $sequenceForClasses = array();

        // If fixtures were already ordered by number then we need
        // to remove classes which are not instances of OrderedFixtureInterface
        // in case fixtures implementing DependentFixtureInterface exist.
        // This is because, in that case, the method orderFixturesByDependencies
        // will handle all fixtures which are not instances of
        // OrderedFixtureInterface
        if ($this->orderFixturesByNumber) {
            $count = count($this->orderedFixtures);

            for ($i = 0 ; $i < $count ; ++$i) {
                if (!($this->orderedFixtures[$i] instanceof OrderedFixtureInterface)) {
                    unset($this->orderedFixtures[$i]);
                }
            }
        }

        // First we determine which classes has dependencies and which don't
        foreach ($this->fixtures as $fixture) {
            $fixtureClass = get_class($fixture);

            if ($fixture instanceof OrderedFixtureInterface) {
                continue;
            } elseif ($fixture instanceof DependentFixtureInterface) {
                $dependenciesClasses = $this->getDependencyClasses($fixture);

                $this->validateDependencies($dependenciesClasses);

                if (!is_array($dependenciesClasses) || empty($dependenciesClasses)) {
                    throw new \InvalidArgumentException(sprintf('Method "%s" in class "%s" must return an array of classes which are dependencies for the fixture, and it must be NOT empty.', 'getDependencies', $fixtureClass));
                }

                if (in_array($fixtureClass, $dependenciesClasses)) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" can\'t have itself as a dependency', $fixtureClass));
                }

                // We mark this class as unsequenced
                $sequenceForClasses[$fixtureClass] = -1;
            } else {
                // This class has no dependencies, so we assign 0
                $sequenceForClasses[$fixtureClass] = 0;
            }
        }

        // Now we order fixtures by sequence
        $sequence = 1;
        $lastCount = -1;

        while (($count = count($unsequencedClasses = $this->getUnsequencedClasses($sequenceForClasses))) > 0 && $count !== $lastCount) {
            foreach ($unsequencedClasses as $key => $class) {
                $fixture = $this->fixtures[$class];
                $dependencies = $this->getDependencyClasses($fixture);
                $unsequencedDependencies = $this->getUnsequencedClasses($sequenceForClasses, $dependencies);

                if (count($unsequencedDependencies) === 0) {
                    $sequenceForClasses[$class] = $sequence++;
                }
            }

            $lastCount = $count;
        }

        $orderedFixtures = array();

        // If there're fixtures unsequenced left and they couldn't be sequenced,
        // it means we have a circular reference
        if ($count > 0) {
            $msg = 'Classes "%s" have produced a CircularReferenceException. ';
            $msg .= 'An example of this problem would be the following: Class C has class B as its dependency. ';
            $msg .= 'Then, class B has class A has its dependency. Finally, class A has class C as its dependency. ';
            $msg .= 'This case would produce a CircularReferenceException.';

            throw new CircularReferenceException(sprintf($msg, implode(',', $unsequencedClasses)));
        } else {
            // We order the classes by sequence
            asort($sequenceForClasses);

            foreach ($sequenceForClasses as $class => $sequence) {
                // If fixtures were ordered
                $orderedFixtures[] = $this->fixtures[$class];
            }
        }

        $this->orderedFixtures = array_merge($this->orderedFixtures, $orderedFixtures);
    }

    private function validateDependencies($dependenciesClasses)
    {

        $loadedFixtureClasses = array_keys($this->fixtures);

        foreach ($dependenciesClasses as $class) {
            if (!in_array($class, $loadedFixtureClasses)) {
                throw new \RuntimeException(sprintf('Fixture "%s" was declared as a dependency, but it should be added in fixture loader first.', $class));
            }
        }

        return true;
    }

    private function getUnsequencedClasses($sequences, $classes = null)
    {
        $unsequencedClasses = array();

        if (is_null($classes)) {
            $classes = array_keys($sequences);
        }

        foreach ($classes as $class) {
            if ($sequences[$class] === -1) {
                $unsequencedClasses[] = $class;
            }
        }

        return $unsequencedClasses;
    }

    /**
     * Return's an array with classnames of the dependencies of the passed fixture.
     *
     * @param \Doctrine\Common\DataFixtures\FixtureInterface $fixture The fixture instance to load the classnames of it's dependencies
     *
     * @return array The array with the classnames of the fixture's dependencies
     */
    private function getDependencyClasses(FixtureInterface $fixture)
    {

        // initialize the array for the class names of the passed fixture's dependencies
        $dependencyClasses = array();

        // query whether or not we've fixture with dependencies
        if ($fixture instanceof DependentFixtureInterface) {
            // load the object manager instance
            $objectManager = $this->getObjectManager();
            // load the class names of the fixture's dependencies
            foreach ($fixture->getDependencies() as $lookupName) {
                if ($objectManager->hasObjectDescriptor($lookupName)) {
                    $dependencyClasses[] = $objectManager->getObjectDescriptor($lookupName)->getClassName();
                }
            }
        }

        // return the classnames of the fixture's dependencies
        return $dependencyClasses;
    }
}
