<?php

namespace Api\Listeners;

use Api\V1\Rest\User\ResetEntity;
use Security\Exception\ChangePasswordException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\Hal\View\HalJsonModel;

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
     * @return void|HalJsonModel
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

        $entity = new ResetEntity($exception->getUser());

        $viewModel = new HalJsonModel();
        $viewModel->setPayload($entity);
        $event->setResult($viewModel);
        $event->setError(false);
        return $viewModel;
    }

}
