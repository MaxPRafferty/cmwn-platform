<?php

namespace Api\Listeners;

use Api\TokenEntityInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Validator\Csrf;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Rest\ResourceEvent;

/**
 * Class CsrfListener
 */
class CsrfListener extends Csrf
{
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'onRender']);
//        $this->listeners[] = $events->attach('ZF\Rest\ResourceInterface', '*', [$this, 'checkToken'], 1000);
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
        $entity  = $event->getParam('entity');
        if (!$entity instanceof TokenEntityInterface) {
            return;
        }

        $entity->setToken($this->getHashFromSession());
    }

    /**
     * @param ResourceEvent $event
     * @return null|ApiProblemResponse
     */
    public function checkToken(ResourceEvent $event)
    {
        if ($event->getRouteMatch()->getMatchedRouteName() === 'api.rest.token') {
            return null;
        }


        /** @var HttpRequest $request */
        $request = $event->getRequest();
        $header  = $request->getHeader('X-Csrf-Token');

        if ($header === false) {
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
}
