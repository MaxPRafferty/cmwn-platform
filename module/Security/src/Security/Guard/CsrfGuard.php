<?php

namespace Security\Guard;

use Api\TokenEntityInterface;
use Application\Utils\NoopLoggerAwareTrait;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Log\LoggerAwareInterface;
use Zend\Validator\Csrf;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Hal\Entity;
use ZF\Rest\ResourceEvent;

/**
 * Class CsrfListener
 */
class CsrfGuard extends Csrf implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     * @todo move to config and allow regex matches
     */
    protected $allowedRoutes = ['api.rest.token', 'api.rest.logout', 'api.rest.forgot', 'api.rest.image'];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
        $this->listeners[] = $events->attach('ZF\Rest\ResourceInterface', '*', [$this, 'checkToken'], 1000);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    /**
     * @param EventInterface $event
     */
    public function onRender(EventInterface $event)
    {
        $entity = $event->getParam('entity');
        $entity = !$entity instanceof TokenEntityInterface && $entity instanceof Entity
            ? $entity->entity
            : $entity;

        if (!$entity instanceof TokenEntityInterface) {
            return;
        }

        $payload = $event->getParam('payload');
        $payload['token'] = $this->getHashFromSession();
    }

    /**
     * @param ResourceEvent $event
     * @return null|ApiProblemResponse
     */
    public function checkToken(ResourceEvent $event)
    {
        if (in_array($event->getRouteMatch()->getMatchedRouteName(), $this->allowedRoutes)) {
            return null;
        }

        /** @var HttpRequest $request */
        $request = $event->getRequest();
        $header  = $request->getHeader('X-CSRF');
        $token   = $header === false ? $request->getPost('_token', null) : $header->getFieldValue();

        if ($token !== $this->getHashFromSession()) {
            $this->getLogger()->alert(
                'Attempt to access the site with an invalid CSRF token',
                ['actual_token' => $token, 'expected_token' => $this->getHashFromSession()]
            );

            return new ApiProblemResponse(new ApiProblem(500, 'Invalid token'));
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
