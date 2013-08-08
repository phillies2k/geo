<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Math\UnitConverter;

/**
 * Interface UnitConverterInterface
 * @package P2\Geo\Math
 */
interface UnitConverterInterface 
{
    /**
     * Converts the given value from the required to the supported unit.
     *
     * @param float $value
     *
     * @return float
     */
    public function convert($value);

    /**
     * Returns true when this unit converter can convert values from the given unit
     *
     * @param string $unit
     *
     * @return boolean
     */
    public function requires($unit);

    /**
     * Returns true when this unit converter supports conversion into the given unit.
     *
     * @param string $unit
     *
     * @return boolean
     */
    public function supports($unit);
}
