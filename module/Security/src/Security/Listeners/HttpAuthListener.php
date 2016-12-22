<?php

namespace Security\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Guard\CsrfGuard;
use Security\SecurityUser;
use Zend\Authentication\Adapter\Http;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class HttpAuthListener
 */
class HttpAuthListener implements AuthenticationServiceAwareInterface, LoggerAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use NoopLoggerAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SecurityUser
     */
    protected $user;

    /**
     * @var Http
     */
    protected $adapter;

    /**
     * @var CsrfGuard
     */
    protected $guard;

    /**
     * HttpAuthListener constructor.
     *
     * @param Http $adapter
     * @param CsrfGuard $guard
     */
    public function __construct(Http $adapter, CsrfGuard $guard)
    {
        $this->adapter = $adapter;
        $this->guard   = $guard;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'onRoute'],
            PHP_INT_MAX
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach($listener, 'Zend\Mvc\Application');
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function onRoute(MvcEvent $event)
    {
        // calling from http
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return;
        }

        // Auth attempt being made
        if (!$request->getHeaders()->has('Authorization')) {
            return;
        }

        // Make sure we have the http response
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $this->adapter->setRequest($request);
        $this->adapter->setResponse($response);

        $result = $this->adapter->authenticate();

        if (!$result->isValid()) {
            return;
        }

        $this->user = new SecurityUser([
            'super'    => true,
            'username' => $result->getIdentity()['username'],
        ]);

        $this->getLogger()->info(
            sprintf('Logging in user %s using BASIC Auth', $this->user->getUserName())
        );

        $this->user->setRole('super');
        /** @var AuthenticationService $auth */
        $auth = $this->getAuthenticationService();
        $auth->setStorage(new NonPersistent());
        $auth->getStorage()->write($this->user);

        // Set a csrf token
        $request
            ->getHeaders()
            ->addHeaderLine('X-CSRF: ' . $this->guard->getHash());
    }
}
