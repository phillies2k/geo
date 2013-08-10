<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Tests\Route;

use P2\Geo\Route\JoinPoint;
use P2\Geo\Route\Route;
use P2\Geo\Location\Coordinate;
use P2\Geo\Environment;

/**
 * UnitTest JoinPointTest
 * @package P2\Geo\Tests\Route
 */
class JoinPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var JoinPoint
     */
    protected $testInstance;

    /**
     * set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->environment = new Environment();

        $location1 = new Coordinate(13, 37);
        $location2 = new Coordinate(37, 13);
        $joinPoint = new Coordinate(13, 38);

        $route1 = new Route($location1);
        $route1->to($joinPoint);

        $route2 = new Route($location2);
        $route2->to($joinPoint);

        $this->testInstance = new JoinPoint($route1, $route2, $joinPoint);
    }

    /**
     * tear down
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->environment = null;
        $this->testInstance = null;
    }

    /**
     * @covers \P2\Geo\Route\JoinPoint::__construct
     * @covers \P2\Geo\Route\JoinPoint::getLocation
     * @covers \P2\Geo\Route\JoinPoint::getRoutes
     * @group s
     */
    public function testJoinPoint()
    {
        $routes = new \ReflectionProperty($this->testInstance, 'routes');
        $this->assertTrue($routes->isProtected());
        $routes->setAccessible(true);

        $location = new \ReflectionProperty($this->testInstance, 'location');
        $this->assertTrue($location->isProtected());
        $location->setAccessible(true);

        $testRoutes = $routes->getValue($this->testInstance);
        $this->assertInternalType('array', $testRoutes);
        $this->assertCount(2, $testRoutes);
        $this->assertInstanceOf('P2\Geo\Route\RouteInterface', $testRoutes[0]);
        $this->assertInstanceOf('P2\Geo\Route\RouteInterface', $testRoutes[1]);
        $this->assertEquals($testRoutes, $this->testInstance->getRoutes());

        $testLocation = $location->getValue($this->testInstance);
        $this->assertInstanceOf('P2\Geo\Location\CoordinateInterface', $testLocation);
        $this->assertEquals($testLocation, $this->testInstance->getLocation());
    }
}
