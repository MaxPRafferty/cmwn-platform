<?php

namespace Security\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class ExpireAuthSessionListener
 *
 * Soft Session expiration
 *
 * The reason
 */
class ExpireAuthSessionListener implements AuthenticationServiceAwareInterface, LoggerAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use NoopLoggerAwareTrait;

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
            $manager->detach('*', $listener);
        }
    }

    /**
     * @return void|ApiProblemResponse
     */
    public function onRoute()
    {
        // Do nothing if not logged in
        if (!$this->getAuthenticationService()->hasIdentity()) {
            $this->container->offsetUnset('last_seen');
            return null;
        }
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $currentTime = $lastSeen = $now->getTimestamp();
        if ($this->container->offsetExists('last_seen')) {
            $lastSeen = $this->container->offsetGet('last_seen');
        }

        $diff = $currentTime - $lastSeen;
        $this->getLogger()->debug('User Currently logged in for', ['seconds' => $diff]);

        $this->container->offsetSet('last_seen', $currentTime);
        if ($diff > static::AUTH_TIMEOUT) {
            $this->getAuthenticationService()->clearIdentity();
            $this->getLogger()->info(
                'User session expired',
                ['current_time' => $currentTime, 'last_seen' => $lastSeen]
            );
            $this->container->offsetUnset('last_seen');
            return new ApiProblemResponse(new ApiProblem(401, 'Expired'));
        }

        return null;
    }
}
