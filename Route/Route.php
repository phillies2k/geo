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
 * Class Route
 * @package P2\Geo\Route
 */
class Route implements RouteInterface
{
    /**
     * @var CoordinateInterface[]
     */
    protected $locations = array();

    /**
     * @param CoordinateInterface $location
     */
    public function __construct(CoordinateInterface $location)
    {
        $this->locations = array($location);
    }

    /**
     * {@inheritdoc}
     */
    public function getLength($unit = null)
    {
        if (count($this->locations) < 2) {

            return 0;
        }

        $locations = $this->locations;
        reset($locations);

        /** @var \P2\Geo\Location\CoordinateInterface $origin */
        $origin = array_shift($locations);
        $length = 0;

        do {
            /** @var \P2\Geo\Location\CoordinateInterface $target */
            $target = array_shift($locations);

            $length += $origin->getDistance($target, $unit);

            $origin = $target;
        } while (count($locations) > 0);

        return $length;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(CoordinateInterface $location)
    {
        foreach ($this->locations as $loc) {
            if ($loc->getLatitude() === $location->getLatitude() &&
                $loc->getLongitude() === $location->getLongitude()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getJoinPoint(RouteInterface $route)
    {
        foreach ($this->locations as $location) {
            if ($route->contains($location)) {

                return new JoinPoint($this, $route, $location);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function to(CoordinateInterface $location)
    {
        if (! $this->contains($location)) {
            $this->locations[] = $location;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocations()
    {
        return $this->locations;
    }
}
