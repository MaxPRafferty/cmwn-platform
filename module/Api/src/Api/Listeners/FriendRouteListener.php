<?php

namespace Api\Listeners;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZF\ContentNegotiation\ParameterDataContainer;

/**
 * Class FriendRouteListener
 */
class FriendRouteListener
{
//
    /**
     * @var array
     */
    protected $listeners = [];


    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -649);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return null;
        }

        if ($event->getRouteMatch()->getMatchedRouteName() !== 'api.rest.friend') {
            return null;
        }

        if ($request->getMethod() !== HttpRequest::METHOD_POST) {
            return null;
        }

        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (! $dataContainer instanceof ParameterDataContainer) {
            return null;
        }

        $userId = $event->getRouteMatch()->getParam('user_id', false);
        $dataContainer->setBodyParam('user_id', $userId);
    }
}
