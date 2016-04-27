<?php

namespace Security\Guard;

use Application\Utils\NoopLoggerAwareTrait;
use Security\OpenRouteTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\Validator\Csrf;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class CsrfGuard
 *
 * Checks XSRF token on requests
 *
 * @package Security\Guard
 */
class XsrfGuard extends Csrf implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;
    use OpenRouteTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * XsrfGuard constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setOpenRoutes(isset($config['open-routes']) ? $config['open-routes'] : []);
        parent::__construct();
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_FINISH, [$this, 'onFinish'], 210);
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -500);
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
     * @return bool
     */
    protected function hasHash()
    {
        $session = $this->getSession();
        return !$session->hash;
    }

    /**
     * Forces the session name
     *
     * @return string
     */
    public function getSessionName()
    {
        return 'CMWN_XSRF';
    }

    /**
     * Generates a new hash if one is not set
     *
     * @return mixed
     */
    protected function getHashFromSession()
    {
        $session = $this->getSession();
        if (!$session->hash) {
            $this->generateHash();
        }

        return $session->hash;
    }

    /**
     * @param MvcEvent $event
     * @return null|void|ApiProblemResponse
     */
    public function onFinish(MvcEvent $event)
    {
        $response = $event->getResponse();

        // Coming in from the console
        if (!$response instanceof HttpResponse) {
            return null;
        }

        $hash = $this->getHashFromSession();
        $cookie = new SetCookie();

        $cookie->setName('XSRF-TOKEN');
        $cookie->setValue($hash);
        $cookie->setPath('/');
        $cookie->setSecure(true);
        $cookie->setHttponly(true);
        $cookie->setDomain('.changemyworldnow.com');
        $response->getHeaders()->addHeader($cookie);
        return null;
    }

    /**
     * @param MvcEvent $event
     * @return null|void|ApiProblemResponse
     */
    public function onRoute(MvcEvent $event)
    {
        if ($this->isRouteOpen($event)) {
            return null;
        }

        /** @var HttpRequest $request */
        $request  = $event->getRequest();

        $cookie = $request->getCookie();
        if ($cookie && $cookie->offsetExists('XSRF-TOKEN')) {
            return $this->verifyToken($cookie);
        }
        
        return null;
    }

    /**
     * @param Cookie $cookie
     * @return null|ApiProblemResponse
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function verifyToken(Cookie $cookie)
    {
        $cookieValue  = $cookie->offsetGet('XSRF-TOKEN');
        $sessionValue = $this->getHashFromSession();
        if ($cookieValue !== $sessionValue) {
            $this->getLogger()->alert(
                'Attempt to access the site with an invalid XSRF token',
                [
                    'actual_token'   => $cookie->offsetGet('XSRF-TOKEN'),
                    'expected_token' => $this->getHashFromSession(),
                    'cookie'         => $_COOKIE
                ]
            );

            return new ApiProblemResponse(new ApiProblem(500, 'Invalid Token'));
        }

        return null;
    }

    /**
     * Get CSRF session token timeout
     */
    public function getTimeout()
    {
        return null;
    }
}
