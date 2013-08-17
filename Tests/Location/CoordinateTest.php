<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Tests\Location;

use P2\Geo\Environment;
use P2\Geo\Location\Coordinate;
use P2\Geo\Math\Math;

/**
 * UnitTest CoordinateTest
 * @package P2\Geo\Tests\Location
 */
class CoordinateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var float
     */
    const TEST_LATITUDE = 12.1234567;

    /**
     * @var float
     */
    const TEST_LONGITUDE = 11.9876543;

    /**
     * @var \P2\Geo\Location\Coordinate
     */
    protected $testInstance;

    /**
     * set up
     */
    public function setUp()
    {
        parent::setUp();

        $this->environment = new Environment();
        $this->testInstance = new Coordinate(static::TEST_LATITUDE, static::TEST_LONGITUDE);
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        parent::tearDown();
        
        $this->testInstance = null;
    }
    
    /**
     * @covers \P2\Geo\Location\Coordinate::__construct
     * @group s
     */
    public function testConstructor()
    {
        $mock = $this
            ->getMockBuilder('P2\Geo\Location\Coordinate')
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('setLatitude')
            ->with($this->equalTo(static::TEST_LATITUDE));

        $mock->expects($this->once())
            ->method('setLongitude')
            ->with($this->equalTo(static::TEST_LONGITUDE));

        $reflection = new \ReflectionClass('P2\Geo\Location\Coordinate');
        $constructor = $reflection->getConstructor();
        $this->assertTrue($constructor->isPublic());
        $constructor->invokeArgs($mock, array(static::TEST_LATITUDE, static::TEST_LONGITUDE));
    }

    /**
     * data provider
     * @return array
     */
    public static function dataProviderLatitudeLongitude()
    {
        return array(
            'testGetSetLatitude' => array('latitude', static::TEST_LATITUDE),
            'testGetSetLongitude' => array('longitude', static::TEST_LONGITUDE),
        );
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::getLatitude
     * @covers \P2\Geo\Location\Coordinate::setLatitude
     * @covers \P2\Geo\Location\Coordinate::getLongitude
     * @covers \P2\Geo\Location\Coordinate::setLongitude
     * @dataProvider dataProviderLatitudeLongitude
     * @group s
     */
    public function testGetSetLatitudeAndLongitude($property, $value, $default = null)
    {
        $reflection = new \ReflectionProperty($this->testInstance, $property);
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $getterMethod = 'get' . ucfirst($property);
        $setterMethod = 'set' . ucfirst($property);

        $this->assertTrue(method_exists($this->testInstance, $getterMethod));
        $this->assertTrue(method_exists($this->testInstance, $setterMethod));

        $this->assertSame($this->testInstance, $this->testInstance->$setterMethod($value));
        $this->assertEquals($value, $reflection->getValue($this->testInstance));
        $this->assertEquals($value, $this->testInstance->$getterMethod());
        $this->assertEquals(deg2rad($value), $this->testInstance->$getterMethod(true));

        $this->setExpectedException('InvalidArgumentException', sprintf('Invalid %s given', $property));
        $this->testInstance->$setterMethod(1200);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::getBoundingCoordinates
     * @group s
     */
    public function testGetBoundingCoordinatesThrowsInvalidArgumentException()
    {
        $distance = 'invalid';

        $this->setExpectedException('InvalidArgumentException', sprintf(
            'The given distance "%s" is not a valid float number.',
            $distance
        ));

        $this->testInstance->getBoundingCoordinates($distance, $this->environment);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::getBoundingCoordinates
     * @group s
     */
    public function testGetBoundingCoordinates()
    {
        $distance = 1337.1337;

        $result = $this->testInstance->getBoundingCoordinates($distance, $this->environment);
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf('P2\Geo\Location\Coordinate', $result[0]);
        $this->assertInstanceOf('P2\Geo\Location\Coordinate', $result[1]);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::getBoundingCoordinates
     * @group s
     */
    public function testGetBoundingCoordinatesOnPole()
    {
        $boundingLatitudes = array(-2,2);
        $distance = 1000;

        $radDistance = $distance / $this->environment->getSphereRadius();

        $mock = $this
            ->getMockBuilder('P2\Geo\Location\Coordinate')
            ->setConstructorArgs(array(static::TEST_LATITUDE, static::TEST_LONGITUDE))
            ->setMethods(array('calculateBoundingLatitudes'))
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('calculateBoundingLatitudes')
            ->with($this->equalTo($radDistance))
            ->will($this->returnValue($boundingLatitudes));

        $reflection = new \ReflectionMethod($mock, 'getBoundingCoordinates');
        $this->assertTrue($reflection->isPublic());
        $this->assertInternalType('array', $reflection->invoke($mock, $distance, $this->environment));


    }

    /**
     * @covers \P2\Geo\Location\Coordinate::calculateDelta
     * @group s
     */
    public function testCalculateDelta()
    {
        $radDistance = 0.815;
        $expected = 0.83952508990033;

        $reflection = new \ReflectionMethod($this->testInstance, 'calculateDelta');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertEquals($expected, $reflection->invoke($this->testInstance, $radDistance));
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::calculateBoundingLongitudes
     * @group s
     */
    public function testCalculateBoundingLongitudesWithLowMinimumLongitude()
    {
        $radDistance = 0.815;
        $delta = 4;
        $radLng = 0.20922403712586;
        $difference = 2 * pi();

        $expected = array(
            $radLng - $delta + $difference,
            $radLng + $delta - $difference
        );

        $mock = $this
            ->getMockBuilder('P2\Geo\Location\Coordinate')
            ->setConstructorArgs(array(static::TEST_LATITUDE, static::TEST_LONGITUDE))
            ->setMethods(array('calculateDelta'))
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('calculateDelta')
            ->with($this->equalTo($radDistance))
            ->will($this->returnValue($delta));

        $reflection = new \ReflectionMethod($mock, 'calculateBoundingLongitudes');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $result = $reflection->invoke($mock, $radDistance);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::calculateBoundingLongitudes
     * @group s
     */
    public function testCalculateBoundingLongitudes()
    {
        $reflection = new \ReflectionMethod($this->testInstance, 'calculateBoundingLongitudes');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $distance = 0.007461562;

        $result = $reflection->invoke($this->testInstance, $distance);
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertInternalType('float', $result[0]);
        $this->assertInternalType('float', $result[1]);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::calculateBoundingLatitudes
     * @group s
     */
    public function testCalculateBoundingLatitudes()
    {
        $reflection = new \ReflectionMethod($this->testInstance, 'calculateBoundingLatitudes');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $distance = 0.007461562;

        $result = $reflection->invoke($this->testInstance, $distance);
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertInternalType('float', $result[0]);
        $this->assertInternalType('float', $result[1]);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::calculateBoundingPole
     * @group s
     */
    public function testCalculateBoundingPole()
    {
        $reflection = new \ReflectionMethod($this->testInstance, 'calculateBoundingPole');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $minLat = -2;
        $maxLat = 2;

        $result = $reflection->invoke($this->testInstance, $minLat, $maxLat);
        $this->assertInternalType('array', $result);
        $this->assertCount(4, $result);
        $this->assertEquals(-pi()/2, $result[0]);
        $this->assertEquals(pi()/2, $result[1]);
        $this->assertEquals(-pi(), $result[2]);
        $this->assertEquals(pi(), $result[3]);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::isWithin
     * @group s
     */
    public function testIsWithin()
    {
        $distance = 50;
        $unit = null;

        $boundingCoordinates = array(
            new Coordinate(static::TEST_LATITUDE, static::TEST_LONGITUDE),
            new Coordinate(static::TEST_LATITUDE, static::TEST_LONGITUDE)
        );

        $mock = $this
            ->getMockBuilder('P2\Geo\Location\Coordinate')
            ->setConstructorArgs(array(static::TEST_LATITUDE, static::TEST_LONGITUDE))
            ->setMethods(array('getBoundingCoordinates'))
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('getBoundingCoordinates')
            ->with($this->equalTo($distance), $this->identicalTo($this->environment), $this->equalTo($unit))
            ->will($this->returnValue($boundingCoordinates));

        $reflection = new \ReflectionMethod($mock, 'isWithin');
        $this->assertTrue($reflection->isPublic());
        $this->assertTrue($reflection->invoke($mock, $this->testInstance, $distance, $this->environment));
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::getDistance
     * @group s
     */
    public function testGetDistance()
    {
        $this->assertEquals(0, $this->testInstance->getDistance($this->testInstance, $this->environment));
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::__toString
     * @group s
     */
    public function testToString()
    {
        $this->assertEquals('N 12° 7\' 24.4", E 11° 59\' 15.6"', (string) $this->testInstance);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::formatCoordinate
     * @group s
     */
    public function testFormatCoordinate()
    {
        $reflection = new \ReflectionMethod($this->testInstance, 'formatCoordinate');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $coordinate = static::TEST_LATITUDE;
        $string = $reflection->invoke($this->testInstance, $coordinate, 'N');
        $this->assertEquals('N 12° 7\' 24.4"', $string);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::createFromString
     * @group s
     */
    public function testCreateFromString()
    {
        $coordinate = Coordinate::createFromString('N 12° 7\' 24.4", E 11° 59\' 15.6"');

        $this->assertEquals(12.123444444444, $coordinate->getLatitude());
        $this->assertEquals(11.987666666667, $coordinate->getLongitude());
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::parseCoordinateString
     * @group s
     */
    public function testParseCoordinateString()
    {
        $coordinate = Coordinate::parseCoordinateString('N 12° 7\' 24.4"');

        $this->assertEquals(12.123444444444, $coordinate);
    }

    /**
     * @covers \P2\Geo\Location\Coordinate::toDecimal
     * @group s
     */
    public function testToDecimal()
    {
        $coordinate = Coordinate::toDecimal(12, 7, 24.4);

        $this->assertEquals(12.123444444444, $coordinate);
    }
}
