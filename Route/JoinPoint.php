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
 * Class JoinPoint
 * @package P2\Geo\Route
 */
class JoinPoint implements JoinPointInterface
{
    /**
     * @var CoordinateInterface
     */
    protected $location;

    /**
     * @var RouteInterface[]
     */
    protected $routes;

    /**
     * @param RouteInterface $source
     * @param RouteInterface $target
     * @param CoordinateInterface $location
     */
    public function __construct(RouteInterface $source, RouteInterface $target, CoordinateInterface $location)
    {
        $this->routes = array($source, $target);
        $this->location = $location;
    }

    /**
     * @return CoordinateInterface
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return RouteInterface[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
