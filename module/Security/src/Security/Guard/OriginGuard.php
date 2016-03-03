<?php

namespace Security\Guard;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;

/**
 * Class OriginGuard
 *
 * Adds the CORS headers to all requests
 *
 * @package Security\Guard
 */
class OriginGuard implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'onFinish'], 200);
    }

    /**
     * Listen to the finish event and set the headers
     *
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $event)
    {
        $response = $event->getResponse();
        // Coming in from the console
        if (!$response instanceof Response) {
            return;
        }

        /** @var Request $request */
        $request = $event->getRequest();

        $response->getHeaders()
            ->addHeaderLine('Access-Control-Allow-Origin', 'https://' . $request->getServer('HTTP_HOST'))
            ->addHeaderLine('Access-Control-Allow-Credentials', 'true')
            ->addHeaderLine('Access-Control-Allow-Methods', 'GET, POST, PATCH, OPTIONS, PUT, DELETE')
            ->addHeaderLine(
                'Access-Control-Allow-Headers',
                'Origin, Content-Type, Authorization, X-Auth-Token, X-CSRF-TOKEN'
            )
            ->addHeaderLine('Access-Control-Max-Age', '28800')
            ->addHeaderLine('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    }
}
