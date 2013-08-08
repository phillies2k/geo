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
 * This interface describes a location on the surface of a sphere.
 *
 * Interface CoordinateInterface
 * @package P2\Geo
 */
interface CoordinateInterface extends LatitudeInterface, LongitudeInterface
{
    /**
     * Returns an array containing two geo locations representing the bounding coordinates of this location for the
     * given distance. Locations with latitudes and longitudes between the one of the bounding coordinates are in range,
     * meaning they have a distance to this location which is lower or equal to the given distance.
     *
     * @param float $distance The distance
     * @param int $unit (optional) Might be one of the Math::UNIT_* constants.
     *
     * @return CoordinateInterface[] The bounding locations (offsets) of this location for a certain distance
     * @throws \InvalidArgumentException
     *
     * @api
     */
    public function getBoundingCoordinates($distance, $unit = null);

    /**
     * Returns the great circle distance (the distance between two points on the surface of a sphere).
     *
     * @param CoordinateInterface $coordinates
     * @param int $unit
     * @return float
     *
     * @api
     */
    public function getDistance(CoordinateInterface $coordinates, $unit = null);

    /**
     * @param CoordinateInterface $coordinates
     * @param float $distance
     * @param int $unit
     *
     * @return bool
     *
     * @api
     */
    public function isWithin(CoordinateInterface $coordinates, $distance, $unit = null);

    /**
     * Returns the environment for this coordinates.
     *
     * @return Environment
     */
    public function getEnvironment();
}
