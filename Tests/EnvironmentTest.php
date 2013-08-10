<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Tests;

use P2\Geo\Environment;
use P2\Geo\Math\UnitConverter\KilometersToMilesConverter;
use P2\Geo\Math\UnitConverter\MilesToKilometersConverter;

/**
 * UnitTest EnvironmentTest
 * @package P2\Geo\Tests
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Environment
     */
    protected $testInstance;

    /**
     * @covers \P2\Geo\Environment::__construct
     * set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->testInstance = new Environment();

        $this->assertEquals(Environment::SPHERE_RADIUS, $this->testInstance->getSphereRadius());
        $this->assertEquals('MI', $this->testInstance->getUnitOfMeasurement());
    }

    /**
     * tear down
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->testInstance = null;
    }

    /**
     * @covers \P2\Geo\Environment::getMeasuringUnits
     * @group s
     */
    public function testGetMeasuringUnits()
    {
        $reflection = new \ReflectionProperty($this->testInstance, 'measuringUnits');
        $this->assertTrue($reflection->isProtected());
        $this->assertTrue($reflection->isStatic());
        $reflection->setAccessible(true);
        $this->assertEquals(
            $reflection->getValue($this->testInstance),
            $reflection->getDeclaringClass()->getMethod('getMeasuringUnits')->invoke($this->testInstance)
        );
    }

    /**
     * @covers \P2\Geo\Environment::registerMeasuringUnit
     * @group s
     */
    public function testRegisterUnit()
    {
        $reflection = new \ReflectionProperty($this->testInstance, 'measuringUnits');
        $reflection->setAccessible(true);

        $reflection->getDeclaringClass()->getMethod('registerMeasuringUnit')->invoke($this->testInstance, 'YA');
        $this->assertContains('YA', $reflection->getValue($this->testInstance));

        $this->setExpectedException(
            '\InvalidArgumentException',
            'The measuring unit must be its 2 characters abbreviation.'
        );

        $reflection->getDeclaringClass()->getMethod('registerMeasuringUnit')->invoke($this->testInstance, 'INVALID');
    }

    /**
     * @covers \P2\Geo\Environment::addUnitConverter
     * @covers \P2\Geo\Environment::getUnitConverter
     * @group s
     */
    public function testUnitConverter()
    {
        $converter = new KilometersToMilesConverter();
        $sourceUnit = 'KM';
        $targetUnit = 'MI';

        $reflection = new \ReflectionProperty($this->testInstance, 'unitConverters');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertEquals(array(), $reflection->getValue($this->testInstance));

        $this->testInstance->addUnitConverter($converter);
        $this->assertEquals(array($converter), $reflection->getValue($this->testInstance));

        $this->testInstance->addUnitConverter($converter);

        $this->assertNull($this->testInstance->getUnitConverter($targetUnit, $sourceUnit));
        $this->assertSame($converter, $this->testInstance->getUnitConverter($sourceUnit, $targetUnit));
    }

    /**
     * @covers \P2\Geo\Environment::getSphereRadius
     * @group s
     */
    public function testGetSphereRadius()
    {
        $reflection = new \ReflectionProperty($this->testInstance, 'sphereRadius');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertEquals(Environment::SPHERE_RADIUS, $reflection->getValue($this->testInstance));
        $this->assertEquals(Environment::SPHERE_RADIUS, $this->testInstance->getSphereRadius());

        $converter = new MilesToKilometersConverter();
        $expected = $converter->convert(Environment::SPHERE_RADIUS);

        $this->testInstance->addUnitConverter($converter);
        $this->assertEquals($expected, $this->testInstance->getSphereRadius('KM'));

        $this->setExpectedException('InvalidArgumentException');
        $this->testInstance->getSphereRadius('XX');
    }

    /**
     * @covers \P2\Geo\Environment::setSphereRadius
     * @group s
     */
    public function testSetSphereRadius()
    {
        $reflection = new \ReflectionProperty($this->testInstance, 'sphereRadius');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertEquals(Environment::SPHERE_RADIUS, $reflection->getValue($this->testInstance));
        $this->assertEquals(Environment::SPHERE_RADIUS, $this->testInstance->getSphereRadius());
        $this->assertSame($this->testInstance, $this->testInstance->setSphereRadius(1337));
        $this->assertEquals(1337, $reflection->getValue($this->testInstance));

        $converter = new KilometersToMilesConverter();
        $this->testInstance->addUnitConverter($converter);

        $this->testInstance->setSphereRadius(1337, 'KM');
        $this->assertEquals($converter->convert(1337), $reflection->getValue($this->testInstance));

        $this->setExpectedException('InvalidArgumentException', 'Cannot find a matching unit converter.');
        $this->testInstance->setSphereRadius(1337, 'XX');
    }

    /**
     * @covers \P2\Geo\Environment::setSphereRadius
     * @group s
     */
    public function testSetSphereRadiusThrowsExceptionOnInvalidSphereRadiusArgument()
    {
        $this->setExpectedException('InvalidArgumentException', 'sphereRadius must be numeric.');
        $this->testInstance->setSphereRadius('invalid');
    }

    /**
     * @covers \P2\Geo\Environment::setUnitOfMeasurement
     * @covers \P2\Geo\Environment::getUnitOfMeasurement
     * @group s
     */
    public function testSetGetUnitOfMeasurement()
    {
        $reflection = new \ReflectionProperty($this->testInstance, 'unitOfMeasurement');
        $this->assertTrue($reflection->isProtected());
        $reflection->setAccessible(true);

        $this->assertEquals('MI', $reflection->getValue($this->testInstance));
        $this->assertEquals('MI', $this->testInstance->getUnitOfMeasurement());

        $this->testInstance->addUnitConverter(new KilometersToMilesConverter());
        $this->testInstance->addUnitConverter(new MilesToKilometersConverter());

        $this->assertSame($this->testInstance, $this->testInstance->setUnitOfMeasurement('KM'));
        $this->assertEquals('KM', $reflection->getValue($this->testInstance));
        $this->assertEquals('KM', $this->testInstance->getUnitOfMeasurement());

        $this->setExpectedException('InvalidArgumentException');
        $this->testInstance->setUnitOfMeasurement('invalid');
    }
}
