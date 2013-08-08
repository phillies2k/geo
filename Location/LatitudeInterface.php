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

/**
 * This interface describes the latitude of a location on the surface of a sphere.
 *
 * Interface LatitudeInterface
 * @package P2\Geo
 */
interface LatitudeInterface 
{
    /**
     * minimum latitude in radians (-pi/2)
     */
    const MIN_LATITUDE = -1.5707963267949;

    /**
     * maximum latitude in radians (pi/2)
     */
    const MAX_LATITUDE = 1.5707963267949;

    /**
     * Sets the latitude in degrees.
     *
     * @param float $latitude The latitude
     *
     * @return $this
     * @throws \InvalidArgumentException When the given latitude is not valid.
     */
    public function setLatitude($latitude);

    /**
     * Returns the latitude in degrees.
     *
     * @param bool $asRadian Set to true, to return the latitude in radians.
     *
     * @return float The latitude
     */
    public function getLatitude($asRadian = false);
}
