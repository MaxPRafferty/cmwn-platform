<?php

namespace Security\Utils;

use Zend\Http\Request;
use Zend\Mvc\MvcEvent;

/**
 * Trait OpenRouteTrait
 *
 * Helps with listeners that need to check if a route is restricted or not
 */
trait OpenRouteTrait
{
    /**
     * @var array
     */
    protected $openRoutes = [];

    /**
     * Sets routes that are allowed to be open
     *
     * @param array $routes
     */
    protected function setOpenRoutes(array $routes)
    {
        $this->openRoutes = $routes;
    }

    /**
     * @param MvcEvent $event
     *
     * @return bool
     */
    protected function isRouteOpen(MvcEvent $event)
    {
        // only checking on HTTP requests
        if (!$event->getRequest() instanceof Request) {
            return true;
        }

        $routeName = $event->getRouteMatch()->getMatchedRouteName();
        if (in_array($routeName, $this->openRoutes)) {
            return true;
        }

        // try regex match
        foreach ($this->openRoutes as $allowed) {
            if (preg_match("/" . $allowed . "/", $routeName)) {
                return true;
            }
        }

        return false;
    }
}
