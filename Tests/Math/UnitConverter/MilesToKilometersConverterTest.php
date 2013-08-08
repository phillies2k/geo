<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Tests\Math\UnitConverter;
use P2\Geo\Math\UnitConverter\MilesToKilometersConverter;


/**
 * UnitTest MilesToKilometersConverterTest
 * @package P2\Geo\Tests\Math\UnitConverter
 */
class MilesToKilometersConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \P2\Geo\Math\UnitConverter\MilesToKilometersConverter::supports
     * @covers \P2\Geo\Math\UnitConverter\MilesToKilometersConverter::requires
     * @covers \P2\Geo\Math\UnitConverter\MilesToKilometersConverter::convert
     */
    public function testConverter()
    {
        $converter = new MilesToKilometersConverter();

        $this->assertTrue($converter->supports('KM'));
        $this->assertTrue($converter->requires('MI'));
        $this->assertEquals(160.9344, $converter->convert(100));

        $expectedException = new \InvalidArgumentException('value must be numeric.');
        $this->setExpectedException(get_class($expectedException), $expectedException->getMessage());

        $converter->convert('foo');
    }
}
