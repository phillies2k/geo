<?php
/**
 * This file is part of the Geo project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Geo\Route;

use P2\Geo\Location\CoordinateInterface;

/**
 * Interface RouteInterface
 * @package P2\Geo
 */
interface RouteInterface 
{
    /**
     * Returns the length of this route.
     *
     * @param null|string $unit
     *
     * @return float
     */
    public function getLength($unit = null);

    /**
     * Returns true when the given location is within this route, false otherwise.
     *
     * @param CoordinateInterface $location
     *
     * @return boolean
     */
    public function contains(CoordinateInterface $location);

    /**
     * Returns the join point to the given route if any.
     *
     * @param RouteInterface $route
     *
     * @return null|JoinPointInterface
     */
    public function getJoinPoint(RouteInterface $route);

    /**
     * Routes to the given coordinate.
     *
     * @param CoordinateInterface $location
     *
     * @return self
     */
    public function to(CoordinateInterface $location);

    /**
     * Returns the locations of this route as array.
     *
     * @return CoordinateInterface[]
     */
    public function getLocations();
}
