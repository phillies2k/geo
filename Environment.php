<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo;

use P2\Geo\Location\Coordinate;
use P2\Geo\Location\CoordinateInterface;
use P2\Geo\Math\UnitConverter\UnitConverterInterface;
use P2\Geo\Math\Units;

/**
 * Class Environment
 * @package P2\Geo\Math
 */
class Environment
{
    /**
     * The radius of the earth in miles.
     */
    const SPHERE_RADIUS = 3958.762079;

    /**
     * Unit kilometers
     */
    const UNIT_KILOMETERS = 'KM';

    /**
     * Unit miles
     */
    const UNIT_MILES = 'MI';

    /**
     * @var array
     */
    protected static $measuringUnits = array(
        self::UNIT_KILOMETERS,
        self::UNIT_MILES,
    );

    /**
     * @param $unit
     * @throws \InvalidArgumentException
     */
    public static function registerMeasuringUnit($unit)
    {
        if (! is_string($unit) || strlen($unit) !== 2) {
            throw new \InvalidArgumentException('The measuring unit must be its 2 characters abbreviation.');
        }

        static::$measuringUnits[] = $unit;
    }

    /**
     * @return array
     */
    public static function getMeasuringUnits()
    {
        return static::$measuringUnits;
    }

    /**
     * @var UnitConverterInterface[]
     */
    protected $unitConverters = array();

    /**
     * @var float
     */
    protected $sphereRadius;

    /**
     * @var string
     */
    protected $unitOfMeasurement;

    /**
     * @param float $sphereRadius
     * @param string $unitOfMeasurement
     */
    public function __construct($sphereRadius = self::SPHERE_RADIUS, $unitOfMeasurement = self::UNIT_MILES)
    {
        $this->setUnitOfMeasurement($unitOfMeasurement);
        $this->setSphereRadius($sphereRadius);
    }

    /**
     * @param UnitConverterInterface $unitConverter
     * @return $this
     */
    public function addUnitConverter(UnitConverterInterface $unitConverter)
    {
        $this->unitConverters[] = $unitConverter;

        return $this;
    }

    /**
     * @param string $sourceUnit
     * @param string $targetUnit
     *
     * @return null|UnitConverterInterface
     */
    public function getUnitConverter($sourceUnit, $targetUnit)
    {
        foreach ($this->unitConverters as $unitConverter) {
            if ($unitConverter->requires($sourceUnit) && $unitConverter->supports($targetUnit)) {

                return $unitConverter;
            }
        }

        return null;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     *
     * @return CoordinateInterface
     */
    public function createLocation($latitude, $longitude)
    {
        return new Coordinate($this, $latitude, $longitude);
    }

    /**
     * @param $sphereRadius
     * @param string $unit
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setSphereRadius($sphereRadius, $unit = null)
    {
        if (! is_numeric($sphereRadius) || $sphereRadius <= 0) {
            throw new \InvalidArgumentException('sphereRadius must be numeric.');
        }

        if ($unit !== null && $unit !== $this->getUnitOfMeasurement()) {
            if (null === $unitConverter = $this->getUnitConverter($unit, $this->getUnitOfMeasurement())) {
                throw new \InvalidArgumentException('Cannot find a matching unit converter.');
            }

            $sphereRadius = $unitConverter->convert($sphereRadius);
        }

        $this->sphereRadius = $sphereRadius;

        return $this;
    }

    /**
     * @param string $unit
     *
     * @return float
     * @throws \InvalidArgumentException
     */
    public function getSphereRadius($unit = null)
    {
        $sphereRadius = $this->sphereRadius;

        if ($unit !== null && $unit !== $this->getUnitOfMeasurement()) {
            if (null === $unitConverter = $this->getUnitConverter($this->getUnitOfMeasurement(), $unit)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Cannot find a unit converter for: "%s" to "%s".',
                        $this->getUnitOfMeasurement(),
                        $unit
                    )
                );
            }

            $sphereRadius = $unitConverter->convert($sphereRadius);
        }

        return $sphereRadius;
    }

    /**
     * @param string $unitOfMeasurement
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setUnitOfMeasurement($unitOfMeasurement)
    {
        if (! in_array($unitOfMeasurement, static::getMeasuringUnits())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid unit of measurement. Please use one of: %s',
                    implode(', ', static::getMeasuringUnits())
                ));
        }

        if ($this->unitOfMeasurement !== null && $this->unitOfMeasurement !== $unitOfMeasurement) {
            $oldMeasuringUnit = $this->unitOfMeasurement;
            $sphereRadius = $this->getSphereRadius();
            $this->unitOfMeasurement = $unitOfMeasurement;
            $this->setSphereRadius($sphereRadius, $oldMeasuringUnit);
        } else {
            $this->unitOfMeasurement = $unitOfMeasurement;
        }


        return $this;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasurement()
    {
        return $this->unitOfMeasurement;
    }
}
