<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Location;

use P2\Geo\Environment;

/**
 * Class Coordinate
 * @package P2\Geo\Location
 */
class Coordinate implements CoordinateInterface
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * Creates a new coordinate for the given latitude and longitude on the given environment.
     *
     * @param Environment $environment
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct(Environment $environment, $latitude, $longitude)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoundingCoordinates($distance, $unit = null)
    {
        if (! is_numeric($distance)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given distance "%s" is not a valid float number.',
                    $distance
                )
            );
        }

        $radius = $this->environment->getSphereRadius($unit);
        $radDistance = (float) $distance/$radius;

        list($minLat, $maxLat) = $this->calculateBoundingLatitudes($radDistance);

        if ($minLat < static::MIN_LATITUDE || $maxLat > static::MAX_LATITUDE) {
            list($minLat, $maxLat, $minLng, $maxLng) = $this->calculateBoundingPole($minLat, $maxLat);
        } else {
            list($minLng, $maxLng) = $this->calculateBoundingLongitudes($radDistance);
        }

        return array(
            $this->environment->createLocation(rad2deg($minLat), rad2deg($minLng)),
            $this->environment->createLocation(rad2deg($maxLat), rad2deg($maxLng))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDistance(CoordinateInterface $coordinates, $unit = null)
    {
        $lat1 = $this->getLatitude(true);
        $lat2 = $coordinates->getLatitude(true);

        return acos(
            sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) *
            cos($this->getLongitude(true) - $coordinates->getLongitude(true))
        ) * $this->environment->getSphereRadius($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function isWithin(CoordinateInterface $coordinates, $distance, $unit = null)
    {
        $boundingCoordinates = $this->getBoundingCoordinates($distance, $unit);

        return
            $coordinates->getLatitude() >= $boundingCoordinates[0]->getLatitude() &&
            $coordinates->getLatitude() <= $boundingCoordinates[1]->getLatitude() &&
            $coordinates->getLongitude() >= $boundingCoordinates[0]->getLongitude() &&
            $coordinates->getLongitude() <= $boundingCoordinates[1]->getLongitude();
    }

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        $radLat = deg2rad($latitude);

        if ($radLat < static::MIN_LATITUDE || $radLat > static::MAX_LATITUDE) {
            throw new \InvalidArgumentException('Invalid latitude given.');
        }

        $this->latitude = $latitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatitude($asRadian = false)
    {
        return $asRadian === true ? deg2rad($this->latitude) : $this->latitude;
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        $radLng = deg2rad($longitude);

        if ($radLng < static::MIN_LONGITUDE || $radLng > static::MAX_LONGITUDE) {
            throw new \InvalidArgumentException('Invalid longitude given.');
        }

        $this->longitude = $longitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongitude($asRadian = false)
    {
        return $asRadian === true ? deg2rad($this->longitude) : $this->longitude;
    }

    /**
     * Returns the environment for this coordinates.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Returns a string representation of this coordinates.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('(%.02f, %.02f)', $this->getLatitude(), $this->getLongitude());
    }

    /**
     * Returns the bounding pole coordinates for the given minimum and maximum latitude.
     *
     * @param float $minLat
     * @param float $maxLat
     * @return array
     */
    protected function calculateBoundingPole($minLat, $maxLat)
    {
        return array(
            max($minLat, static::MIN_LATITUDE),
            min($maxLat, static::MAX_LATITUDE),
            static::MIN_LONGITUDE,
            static::MAX_LONGITUDE
        );
    }

    /**
     * Returns the bounding latitude values for the given relative distance within this environment in radians.
     *
     * @param float $radDistance The relative distance in radians
     * @return array
     */
    protected function calculateBoundingLatitudes($radDistance)
    {
        $minLat = $this->getLatitude(true) - $radDistance;
        $maxLat = $this->getLatitude(true) + $radDistance;

        return array($minLat, $maxLat);
    }

    /**
     * Returns the bounding longitude values for the given relative distance within this environment in radians.
     *
     * @param float $radDistance The relative distance in radians
     * @return array
     */
    protected function calculateBoundingLongitudes($radDistance)
    {
        $delta = $this->calculateDelta($radDistance);

        $minLng = $this->getLongitude(true) - $delta;
        $maxLng = $this->getLongitude(true) + $delta;

        if ($minLng < static::MIN_LONGITUDE) {
            $minLng += 2 * pi();
        }

        if ($maxLng > static::MAX_LONGITUDE) {
            $maxLng -= 2 * pi();
        }

        return array($minLng, $maxLng);
    }

    /**
     * Returns the distance delta for the given distance to this location.
     *
     * @param float $radDistance
     * @return float
     */
    protected function calculateDelta($radDistance)
    {
        return asin(sin($radDistance) / cos($this->getLatitude(true)));
    }
}
