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
     * @var string
     */
    const PATTERN_DMS = '/(N|E|S|W)\s(\d+)°\s(\d+)\'\s([0-9]+(\.[0-9]+)?)"/';

    /**
     * @var string
     */
    const FORMAT_DMS = 'DMS';

    /**
     * @var string
     */
    const FORMAT_DEG = 'DEG';

    /**
     * @var string
     */
    const FORMAT_RAD = 'RAD';

    /**
     * @var int
     */
    const LATITUDE = 1;

    /**
     * @var int
     */
    const LONGITUDE = 2;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @param string $coordinate
     *
     * @return Coordinate
     */
    public static function createFromString($coordinate)
    {
        list($latitude, $longitude) = explode(', ', $coordinate);

        return new static(
            static::parseDMSString($latitude),
            static::parseDMSString($longitude)
        );
    }

    /**
     * Returns the decimal representation of the given DMS string.
     *
     * @param string $coordinate
     *
     * @return float
     */
    public static function parseDMSString($coordinate)
    {
        preg_match(static::PATTERN_DMS, $coordinate, $matches);
        list (, $direction, $degrees, $minutes, $seconds) = $matches;
        $decimal = static::dmsToDec($degrees, $minutes, $seconds);
        return $decimal * (in_array($direction, array('S', 'W')) ? -1: 1);
    }

    /**
     * @param float $coordinate
     * @return string
     */
    public static function decToDMS($coordinate)
    {
        $vars = explode('.', (string) $coordinate);
        $deg = $vars[0];
        $tmp = '0.' . $vars[1];
        $tmp = $tmp * 3600;
        $min = floor($tmp/60);
        $sec = $tmp - ($min*60);

        return sprintf('%d° %d\' %01.1f"', $deg, $min, $sec);
    }

    /**
     * @param $degrees
     * @param $minutes
     * @param $seconds
     * @return mixed
     */
    public static function dmsToDec($degrees, $minutes, $seconds)
    {
        return $degrees + ((($minutes*60) + $seconds)/3600);
    }

    /**
     * Creates a new coordinate for the given latitude and longitude on the given environment.
     *
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
    }

    /**
     * {@inheritdoc}
     */
    public function getBoundingCoordinates($distance, Environment $environment, $unit = null)
    {
        if (! is_numeric($distance)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given distance "%s" is not a valid float number.',
                    $distance
                )
            );
        }

        $radius = $environment->getSphereRadius($unit);
        $radDistance = (float) $distance/$radius;

        list($minLat, $maxLat) = $this->calculateBoundingLatitudes($radDistance);

        if ($minLat < static::MIN_LATITUDE || $maxLat > static::MAX_LATITUDE) {
            list($minLat, $maxLat, $minLng, $maxLng) = $this->calculateBoundingPole($minLat, $maxLat);
        } else {
            list($minLng, $maxLng) = $this->calculateBoundingLongitudes($radDistance);
        }

        return array(
            new Coordinate(rad2deg($minLat), rad2deg($minLng)),
            new Coordinate(rad2deg($maxLat), rad2deg($maxLng))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDistance(CoordinateInterface $coordinates, Environment $environment, $unit = null)
    {
        $lat1 = $this->getLatitude(true);
        $lat2 = $coordinates->getLatitude(true);

        return acos(
            sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) *
            cos($this->getLongitude(true) - $coordinates->getLongitude(true))
        ) * $environment->getSphereRadius($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function isWithin(CoordinateInterface $coordinates, $distance, Environment $environment, $unit = null)
    {
        $boundingCoordinates = $this->getBoundingCoordinates($distance, $environment, $unit);

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
     * @param null $type
     * @return float|string
     * @throws \InvalidArgumentException
     */
    public function toDeg($type = null)
    {
        switch ($type) {
            case static::LATITUDE:
                return $this->getLatitude();
            case static::LONGITUDE:
                return $this->getLongitude();
            case null:
                return $this->getLatitude() . ', ' . $this->getLongitude();
            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * @param null $type
     * @return float|string
     * @throws \InvalidArgumentException
     */
    public function toRad($type = null)
    {
        switch ($type) {
            case static::LATITUDE:
                return $this->getLatitude(true);
            case static::LONGITUDE:
                return $this->getLongitude(true);
            case null:
                return $this->getLatitude(true) . ', ' . $this->getLongitude(true);
            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * @param null $type
     * @return string
     * @throws \InvalidArgumentException
     */
    public function toDMS($type = null)
    {
        switch ($type) {
            case static::LATITUDE:
                $coordinate = $this->getLatitude();
                $dir = $coordinate < 0 ? 'S' : 'N';
                break;
            case static::LONGITUDE:
                $coordinate = $this->getLongitude();
                $dir = $coordinate < 0 ? 'W' : 'E';
                break;
            case null:
                return $this->__toString();
            default:
                throw new \InvalidArgumentException();
        }

        return $dir . ' ' . static::decToDMS($coordinate);
    }

    /**
     * Returns a string representation of this coordinates.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s, %s', $this->toDMS(static::LATITUDE), $this->toDMS(static::LONGITUDE));
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
