<?php

namespace Api\Listeners;

use Security\Exception\ChangePasswordException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class ChangePasswordListener
 *
 * @package Api\Listeners
 */
class ChangePasswordListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError']);
    }

    /**
     * @param MvcEvent $event
     * @return void
     */
    public function onError(MvcEvent $event)
    {
        if (!$event->isError()) {
            return;
        }

        $exception = $event->getParam('exception');
        if (!$exception instanceof ChangePasswordException) {
            return;
        }

        $event->setResult(new ApiProblemResponse(new ApiProblem(401, $exception->getMessage())));
        $event->setError(false);
    }
}
