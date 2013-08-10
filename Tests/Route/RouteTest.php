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

use P2\Geo\Environment;
use P2\Geo\Location\Coordinate;
use P2\Geo\Route\Route;

/**
 * UnitTest RouteTest
 * @package P2\Geo\Tests\Route
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var Route
     */
    protected $testInstance;

    /**
     * set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->environment = new Environment();

        $location = new Coordinate(13,37);
        $this->testInstance = new Route($location);
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
     * @covers \P2\Geo\Route\Route::__construct
     * @covers \P2\Geo\Route\Route::getLocations
     * @group s
     */
    public function testConstructor()
    {
        $location = new Coordinate(13, 37);
        $reflection = new \ReflectionProperty('P2\Geo\Route\Route', 'locations');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $route = new Route($location);
        $value = $reflection->getValue($route);
        $this->assertInternalType('array', $value);
        $this->assertCount(1, $value);
        $this->assertSame($location, $value[0]);
        $this->assertEquals($value, $route->getLocations());
    }

    /**
     * @covers \P2\Geo\Route\Route::getLength
     * @group s
     */
    public function testGetLength()
    {
        $location = new Coordinate(37, 13);

        $this->assertEquals(0, $this->testInstance->getLength($this->environment));
        $this->testInstance->to($location);

        $locations = $this->testInstance->getLocations();

        $this->assertEquals(
            $locations[0]->getDistance($location, $this->environment),
            $this->testInstance->getLength($this->environment)
        );
    }

    /**
     * @covers \P2\Geo\Route\Route::contains
     * @group s
     */
    public function testContains()
    {
        $location = new Coordinate(37, 13);
        $this->assertFalse($this->testInstance->contains($location));
        $this->testInstance->to($location);
        $this->assertTrue($this->testInstance->contains($location));
    }

    /**
     * @covers \P2\Geo\Route\Route::getJoinPoint
     * @group s
     */
    public function testGetJoinPoint()
    {
        $locations = $this->testInstance->getLocations();
        $joinLocation = $locations[0];

        $location = new Coordinate(37, 13);
        $route = new Route($location);

        $this->assertNull($this->testInstance->getJoinPoint($route));

        $route->to($joinLocation);

        $this->assertInstanceOf('P2\Geo\Route\JoinPointInterface', $this->testInstance->getJoinPoint($route));
    }

    /**
     * @covers \P2\Geo\Route\Route::to
     * @group s
     */
    public function testTo()
    {

        $locations = $this->testInstance->getLocations();
        $sameLocation = $locations[0];

        $reflection = new \ReflectionProperty($this->testInstance, 'locations');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertSame($this->testInstance, $this->testInstance->to($sameLocation));
        $this->assertEquals($locations, $reflection->getValue($this->testInstance));

        $location = new Coordinate(37, 13);
        $this->assertSame($this->testInstance, $this->testInstance->to($location));
        $newLocations = $reflection->getValue($this->testInstance);
        $this->assertInternalType('array', $newLocations);
        $this->assertCount(2, $newLocations);
        $this->assertContains($location, $newLocations);
        $this->assertContains($sameLocation, $newLocations);
    }
}
