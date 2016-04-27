<?php

namespace Application\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class ErrorListener
 */
class ErrorListener implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * Default types to match in Accept header
     *
     * @var array
     */
    protected $acceptFilters = [
        '*/*',
    ];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'dispatchError']);
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_RENDER_ERROR, [$this, 'renderError']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach('*', $listener);
        }
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function dispatchError(MvcEvent $mvcEvent)
    {
        $exception = $this->getException($mvcEvent);
        $this->getLogger()->err(
            sprintf('Dispatch Error: %s', $exception->getMessage()),
            $exception->getTrace()
        );
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function renderError(MvcEvent $mvcEvent)
    {
        $exception = $this->getException($mvcEvent);
        $this->getLogger()->err(
            sprintf('Render Error: %s', $exception->getMessage()),
            $exception->getTrace()
        );
    }

    /**
     * @param MvcEvent $mvcEvent
     * @return \Exception|mixed
     */
    protected function getException(MvcEvent $mvcEvent)
    {
        $exception = $mvcEvent->getParam('exception');
        if (!$exception instanceof \Exception) {
            $exception = new \Exception($mvcEvent->getError());
        }

        return $exception;
    }
}
