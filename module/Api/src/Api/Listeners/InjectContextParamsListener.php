<?php

namespace Api\Listeners;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use ZF\ContentNegotiation\ParameterDataContainer;

/**
 * Listener to inject route params into the context
 */
class InjectContextParamsListener
{
    /**
     * @var
     */
    protected $listener;

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listener = $events->attach(
            Application::class,
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
        $manager->detach($this->listener);
    }

    /**
     * @param MvcEvent $event
     * @return void
     */
    public function injectRouteParams(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request || $request->getMethod() === 'PATCH') {
            return;
        }

        $route  = $event->getRouteMatch();
        $routeParams = $route->getParams();

        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (!$dataContainer instanceof ParameterDataContainer) {
            return;
        }

        array_walk($routeParams, function ($routeParamValue, $routeParamName) use (&$dataContainer) {
            if (!$dataContainer->hasBodyParam($routeParamName)) {
                $dataContainer->setBodyParam($routeParamName, $routeParamValue);
            }
        });
    }
}
