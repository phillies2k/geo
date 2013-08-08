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
 * This interface describes the longitude of a location on the surface of a sphere.
 *
 * Interface LongitudeInterface
 * @package P2\Geo\Location
 */
interface LongitudeInterface 
{
    /**
     * minimum longitude in radians
     */
    const MIN_LONGITUDE = -3.1415926535898;

    /**
     * maximum longitude in radians
     */
    const MAX_LONGITUDE = 3.1415926535898;

    /**
     * Sets the longitude in degrees.
     *
     * @param float $longitude The longitude
     *
     * @return $this
     * @throws \InvalidArgumentException When the given longitude is not valid.
     */
    public function setLongitude($longitude);

    /**
     * Returns the longitude in degrees.
     *
     * @param boolean $asRadian Set to true, to return the longitude in radians.
     *
     * @return float The longitude
     */
    public function getLongitude($asRadian = false);
}
