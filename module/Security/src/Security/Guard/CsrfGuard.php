<?php

namespace Security\Guard;

use Api\TokenEntityInterface;
use Application\Utils\NoopLoggerAwareTrait;
use Security\Utils\OpenRouteTrait;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\Validator\Csrf;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Hal\Entity;

/**
 * Checks the Csrf Token is correct
 *
 * @todo remove when we get JWT going
 */
class CsrfGuard extends Csrf implements LoggerAwareInterface
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
        $config = $config['cmwn-security'] ?? [];
        $this->setOpenRoutes(isset($config['open-routes']) ? $config['open-routes'] : []);
        parent::__construct();
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, [$this, 'checkToken'], 0);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach($this->listeners[0], 'ZF\Hal\Plugin\Hal');
        $manager->detach($this->listeners[1], 'Zend\Mvc\Application');
    }

    /**
     * @param EventInterface $event
     */
    public function onRender(EventInterface $event)
    {
        $entity = $event->getParam('entity');
        $entity = !$entity instanceof TokenEntityInterface && $entity instanceof Entity
            ? $entity->getEntity()
            : $entity;

        if (!$entity instanceof TokenEntityInterface) {
            return;
        }

        $payload = $event->getParam('payload');
        $payload['token'] = $this->getHashFromSession();
    }

    /**
     * @param MvcEvent $event
     * @return null|ApiProblemResponse
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function checkToken(MvcEvent $event)
    {
        if ($this->isRouteOpen($event)) {
            return null;
        }

        /** @var HttpRequest $request */
        $request = $event->getRequest();

        // Dont check on options
        if ($request->getMethod() === $request::METHOD_OPTIONS) {
            return null;
        }

        $header  = $request->getHeader('X-CSRF');
        $token   = $header === false ? $request->getPost('_token', null) : $header->getFieldValue();

        if ($token !== $this->getHashFromSession()) {
            $this->getLogger()->alert(
                'Attempt to access the site with an invalid CSRF token',
                [
                    'actual_token'   => $token,
                    'expected_token' => $this->getHashFromSession(),
                    'cookie'         => $_COOKIE
                ]
            );

            return new ApiProblemResponse(new ApiProblem(500, 'Invalid token'));
        }

        return null;
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
        return 'CMWN_CSRF';
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
     * Get CSRF session token timeout
     */
    public function getTimeout()
    {
        return null;
    }
}
