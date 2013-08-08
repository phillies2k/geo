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

use P2\Geo\Math\UnitConverter\KilometersToMilesConverter;


/**
 * UnitTest KilometersToMilesConverterTest
 * @package P2\Geo\Tests\Math\UnitConverter
 */
class KilometersToMilesConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \P2\Geo\Math\UnitConverter\KilometersToMilesConverter::supports
     * @covers \P2\Geo\Math\UnitConverter\KilometersToMilesConverter::requires
     * @covers \P2\Geo\Math\UnitConverter\KilometersToMilesConverter::convert
     */
    public function testConverter()
    {
        $converter = new KilometersToMilesConverter();

        $this->assertTrue($converter->supports('MI'));
        $this->assertTrue($converter->requires('KM'));
        $this->assertEquals(100, $converter->convert(160.9344));

        $expectedException = new \InvalidArgumentException('value must be numeric.');
        $this->setExpectedException(get_class($expectedException), $expectedException->getMessage());

        $converter->convert('foo');
    }
}
