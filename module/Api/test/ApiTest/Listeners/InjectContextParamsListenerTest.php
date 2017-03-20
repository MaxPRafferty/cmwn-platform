<?php

namespace ApiTest\Listeners;

use Api\Listeners\InjectContextParamsListener;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use ZF\ContentNegotiation\ParameterDataContainer;

/**
 * Unit tests for InjectContextParamsListener
 */
class InjectContextParamsListenerTest extends TestCase
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @before
     */
    public function setUpEvent()
    {
        $event = new MvcEvent();
        $event->setName(MvcEvent::EVENT_ROUTE);
        $routeMatch = new RouteMatch(['foo' => 'bar']);
        $event->setRouteMatch($routeMatch);
        $request = new Request();
        $event->setRequest($request);
        $event->setParam('ZFContentNegotiationParameterData', new ParameterDataContainer());
        $this->event = $event;
    }

    /**
     * @before
     */
    public function setUpEventManager()
    {
        $this->eventManager = new EventManager();
        $this->eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            [new InjectContextParamsListener(), 'injectRouteParams'],
            -630
        );
    }

    /**
     * @test
     */
    public function testItShouldInjectRouteParamsToEventContext()
    {
        $this->event->getRequest()->setMethod(Request::METHOD_GET);
        $this->eventManager->triggerEvent($this->event);
        $contextData = $this->event->getParam('ZFContentNegotiationParameterData', false);
        $this->assertTrue($contextData->hasBodyParam('foo'), 'it did not inject route params to the context');
    }

    /**
     * @test
     */
    public function testItShouldNotInjectParamsIfMethodIsPatch()
    {
        $this->event->getRequest()->setMethod(Request::METHOD_PATCH);
        $this->eventManager->triggerEvent($this->event);
        $contextData = $this->event->getParam('ZFContentNegotiationParameterData', false);
        $this->assertFalse($contextData->hasBodyParam('foo'), 'it injected params when request method id patch');
    }

    /**
     * @test
     */
    public function testItShouldNotInjectParamsIfNoContextFound()
    {
        $this->event->getRequest()->setMethod(Request::METHOD_GET);
        $this->event->setParam('ZFContentNegotiationParameterData', null);
        $this->eventManager->triggerEvent($this->event);
        $contextData = $this->event->getParam('ZFContentNegotiationParameterData', false);
        $this->assertFalse($contextData instanceof ParameterDataContainer, 'data container was set incorrectly');
    }
}
