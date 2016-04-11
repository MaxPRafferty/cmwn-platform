<?php

namespace Security\Listeners;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class ExpireAuthSessionListener
 */
class ExpireAuthSessionListener implements AuthenticationServiceAwareInterface, LoggerAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use LoggerAwareTrait;

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
            $this->getLogger()->debug('[esl] No user currently logged in');
            return;
        }

        $currentTime = $lastSeen = strtotime('now');
        if ($this->container->offsetExists('last_seen')) {
            $this->getLogger()->debug('[esl] Last seen set on session');
            $lastSeen = $this->container->offsetGet('last_seen');
        }

        $this->getLogger()->debug('');
        $diff = $currentTime - $lastSeen;

        $this->container->offsetSet('last_seen', $lastSeen);
        if ($diff > static::AUTH_TIMEOUT) {
            $this->getAuthenticationService()->clearIdentity();
            $this->getLogger()->info(
                '[esl] User session expired',
                ['current_time' => $currentTime, 'last_seen' => $lastSeen]
            );
            return new ApiProblemResponse(new ApiProblem(401, 'Expired'));
        }
    }
}

