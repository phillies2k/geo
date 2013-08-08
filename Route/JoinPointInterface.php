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
use P2\Geo\Route\RouteInterface;

/**
 * Interface JoinPointInterface
 * @package P2\Geo\Route
 */
interface JoinPointInterface 
{
    /**
     * Returns the location of this join point
     *
     * @return CoordinateInterface
     */
    public function getLocation();

    /**
     * Returns an array of routes that join at this join point.
     *
     * @return RouteInterface[]
     */
    public function getRoutes();
}
