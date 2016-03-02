<?php

namespace Security\Guard;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\Validator\Csrf;

class CsrfGuard extends Csrf implements ListenerAggregateInterface
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
        // TODO attach when you get workflow from apigility
//        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'setCookie'], 210);
    }

    public function getSessionName()
    {
        return 'CMWN_XSRF';
    }

    protected function getHashFromSession()
    {
        $session = $this->getSession();
        if (!$session->hash) {
            $this->generateHash();
        }

        return $session->hash;
    }

    public function setCookie(MvcEvent $event)
    {
        $response = $event->getResponse();
        /** @var Request $request */
        $request  = $event->getRequest();

        // Coming in from the console
        if (!$response instanceof Response) {
            return;
        }

        $cookie = $request->getCookie();
        if ($cookie && $cookie->offsetExists('XSRF-TOKEN')) {
            return;
        }

        $cookie = new SetCookie(
            'XSRF-TOKEN',
            $this->getHashFromSession(),
            time() + 60 * 120,
            '/',
            $event->getRequest()->getServer('HTTP_HOST'),
            true
        );

        $response->getHeaders()->addHeader($cookie);
    }
}
