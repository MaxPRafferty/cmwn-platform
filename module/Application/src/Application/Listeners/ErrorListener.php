<?php

namespace Application\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\Application;
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
        $this->listeners[] = $events->attach(
            Application::class,
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'dispatchError'],
            PHP_INT_MAX
        );

        $this->listeners[] = $events->attach(
            Application::class,
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'renderError'],
            PHP_INT_MAX
        );
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener, '\Zend\Mvc\Application');
        }
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function dispatchError(MvcEvent $mvcEvent)
    {
        $exception = $this->getException($mvcEvent);
        if ($exception) {
            $this->getLogger()->crit(
                sprintf('Dispatch Error: %s', $exception->getMessage()),
                ['exception' => $exception]
            );
        }
    }

    /**
     * @param MvcEvent $mvcEvent
     */
    public function renderError(MvcEvent $mvcEvent)
    {
        $exception = $this->getException($mvcEvent);
        if ($exception) {
            $this->getLogger()->crit(
                sprintf('Render Error: %s', $exception->getMessage()),
                ['exception' => $exception]
            );
        }
    }

    /**
     * @param MvcEvent $mvcEvent
     * @return \Exception|mixed
     */
    protected function getException(MvcEvent $mvcEvent)
    {
        return $mvcEvent->getParam('exception', false);
    }
}
