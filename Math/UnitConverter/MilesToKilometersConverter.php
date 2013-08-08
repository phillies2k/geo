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
 * Class MilesToKilometersConverter
 * @package P2\Geo\Math\UnitConverter
 */
class MilesToKilometersConverter implements UnitConverterInterface
{
    /**
     * miles to kilometers convert factor
     */
    const MILES_TO_KILOMETERS = 1.609343999999999;

    public function convert($value)
    {
        if (! is_numeric($value)) {
            throw new \InvalidArgumentException('value must be numeric.');
        }

        return (float) $value * static::MILES_TO_KILOMETERS;
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
        return $unit === Environment::UNIT_MILES;
    }

    public function supports($unit)
    {
        return $unit === Environment::UNIT_KILOMETERS;
    }
}
