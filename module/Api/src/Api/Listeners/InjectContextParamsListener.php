<?php

namespace Api\Listeners;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ContentNegotiation\ParameterDataContainer;

/**
 * Class UserParamListener
 * @package Api\Listeners
 */
class InjectContextParamsListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners['Zend\Mvc\Application'] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'injectRouteParams'],
            -630
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $eventId => $listener) {
            $manager->detach($eventId, $listener);
        }
    }

    /**
     * @param MvcEvent $event
     * @return null|void
     */
    public function injectRouteParams(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request || $request->getMethod() === 'PATCH') {
            return ;
        }

        $route  = $event->getRouteMatch();
        $routeParams = $route->getParams();

        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (!$dataContainer instanceof ParameterDataContainer) {
            return null;
        }

        array_walk($routeParams, function ($routeParamValue, $routeParamName) use (&$dataContainer) {
            if (!$dataContainer->hasBodyParam($routeParamName)) {
                $dataContainer->setBodyParam($routeParamName, $routeParamValue);
            }
        });
    }
}
