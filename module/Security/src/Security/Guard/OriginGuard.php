<?php

namespace Security\Guard;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Console\Request as ConsoleRequest;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class OriginGuard
 *
 * Adds the CORS headers to all requests
 *
 * @package Security\Guard
 */
class OriginGuard implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_FINISH, [$this, 'attachCors'], 200);
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'attachCors'], 200);
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
     * Listen to the finish event and set the headers
     *
     * @param MvcEvent $event
     */
    public function attachCors(MvcEvent $event)
    {
        /** @var Request $request */
        $request  = $event->getRequest();
        // Coming in from the console
        if ($event->getRequest() instanceof ConsoleRequest) {
            return;
        }

        /** @var Response $response */
        $response = $event->getResponse();
        $origin = $request->getServer('HTTP_ORIGIN', '');

        // THOUGHT Config?
        if (preg_match("/^https:\/\/([0-9a-zA-Z-_]+)?\.changemyworldnow\.com(:[0-9]+)?\/?$/i", $origin)) {
            $this->getLogger()->debug('Setting Access-Control-Allow-Origin from header');
            $response->getHeaders()
                ->addHeaderLine('Access-Control-Allow-Origin', $origin);
        } else {
            $this->getLogger()->debug('Setting Access-Control-Allow-Origin to default');
            $response->getHeaders()
                ->addHeaderLine('Access-Control-Allow-Origin', 'https://' . $request->getServer('HTTP_HOST'));
        }

        $response->getHeaders()
            ->addHeaderLine('Access-Control-Allow-Credentials', 'true')
            ->addHeaderLine('Access-Control-Allow-Methods', 'GET, POST, PATCH, OPTIONS, PUT, DELETE')
            ->addHeaderLine(
                'Access-Control-Allow-Headers',
                'Origin, Content-Type, X-CSRF'
            )
            ->addHeaderLine('Access-Control-Max-Age', '28800')
            ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }
}
