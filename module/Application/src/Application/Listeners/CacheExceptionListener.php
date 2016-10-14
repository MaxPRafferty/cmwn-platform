<?php

namespace Application\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\ExceptionEvent;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;

/**
 * Class CacheExceptionListener
 */
class CacheExceptionListener implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            AbstractAdapter::class,
            '*',
            [$this, 'cacheError'],
            PHP_INT_MAX
        );
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach(AbstractAdapter::class, $listener);
        }
    }

    /**
     * @param EventInterface $event
     */
    public function cacheError(EventInterface $event)
    {
        if (!$event instanceof ExceptionEvent) {
            return;
        }

        $event->setThrowException(false);
        $exception = $event->getException();
        $logMessages = [];

        do {
            $priority = Logger::ERR;
            $extra = [
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
            if (isset($exception->xdebug_message)) {
                $extra['xdebug'] = $exception->xdebug_message;
            }

            $logMessages[] = [
                'priority' => $priority,
                'message'  => $exception->getMessage(),
                'extra'    => $extra,
            ];
            $exception = $exception->getPrevious();
        } while ($exception);

        foreach (array_reverse($logMessages) as $logMessage) {
            $this->getLogger()->log($logMessage['priority'], $logMessage['message'], $logMessage['extra']);
        }
    }
}
