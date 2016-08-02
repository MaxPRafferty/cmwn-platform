<?php

namespace Security\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use Zend\Authentication\Adapter\Http;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Storage\ArrayStorage;

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

//        $this->listeners[] = $events->attach(
//            'Zend\Mvc\Application',
//            MvcEvent::EVENT_FINISH,
//            [$this, 'onFinish']
//        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach('Zend\Mvc\Application', $listener);
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

        $config = [
            'accept_schemes' => 'basic',
            'realm'          => 'cmwn',
            'digest_domains' => '/lambda',
            'nonce_timeout'  => 3600,
        ];

        $adapter  = new Http($config);
        $resolver = new Http\FileResolver();
        $resolver->setFile(getcwd() . '/data/files/.htpasswd-lambda');

        $adapter->setBasicResolver($resolver);

        $adapter->setRequest($request);
        $adapter->setResponse($response);

        $result = $adapter->authenticate();

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
    }
}
