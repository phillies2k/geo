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

use P2\Geo\Environment;

/**
 * Class KilometersToMilesConverter
 * @package P2\Geo\Math\UnitConverter
 */
class KilometersToMilesConverter implements UnitConverterInterface
{
    /**
     * kilometers to miles convert factor
     */
    const KILOMETERS_TO_MILES = 0.621371192237334;

    public function convert($value)
    {
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException('value must be numeric.');
        }

        return (float) $value * static::KILOMETERS_TO_MILES;
    }

    /**
     * Returns true when this unit converter can convert values in the given unit
     *
     * @param string $unit
     *
     * @return boolean
     */
    public function requires($unit)
    {
        return $unit === Environment::UNIT_KILOMETERS;
    }

    public function supports($unit)
    {
        return $unit === Environment::UNIT_MILES;
    }
}
