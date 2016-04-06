<?php

namespace Security\Listeners;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class ExpireAuthSessionListener
 */
class ExpireAuthSessionListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * How many seconds to keep an authenticated session active
     */
    const AUTH_TIMEOUT = 600;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * ExpireAuthSessionListener constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            '*',
            MvcEvent::EVENT_ROUTE,
            [$this, 'onRoute']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach('*', $listener);
        }
    }

    /**
     * @param MvcEvent $event
     *
     * @return void|ApiProblemResponse
     */
    public function onRoute(MvcEvent $event)
    {
        // Do nothing if not logged in
        if (!$this->getAuthenticationService()->hasIdentity()) {
            $this->container->offsetUnset('last_seen');
            return;
        }

        $currentTime = strtotime('now');
        $lastSeen    = $this->container->offsetExists('last_seen')
            ? $this->container->offsetGet('last_seen')
            : $currentTime;

        $diff = $currentTime - $lastSeen;

        $this->container->offsetSet('last_seen', $lastSeen);
        if ($diff > static::AUTH_TIMEOUT) {
            $this->getAuthenticationService()->clearIdentity();
            return new ApiProblemResponse(new ApiProblem(401, 'Expired'));
        }
    }
}

// 3371b7bd3edeb996fa3169ea15b6f0d2-da230e4727dee392bab6338ce8e2e282
// 3371b7bd3edeb996fa3169ea15b6f0d2-da230e4727dee392bab6338ce8e2e282
// 3371b7bd3edeb996fa3169ea15b6f0d2-da230e4727dee392bab6338ce8e2e282

// 5a123354acb54d19ef0a0aec69f39b34-40296be49040e8866050566e60786fac
// f977202caf9304f470154ae85d7529cc-eef9ebc4b811d19a7d39b979b57960a6

