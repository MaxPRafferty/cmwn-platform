<?php

namespace Api\Listeners;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class ChangePasswordListener
 *
 * @package Api\Listeners
 */
class ChangePasswordListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            -PHP_INT_MAX
        );

        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'onError'],
            150
        );

        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'onError'],
            150
        );
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
     * @param MvcEvent $event
     * @return void
     */
    public function onError(MvcEvent $event)
    {
        if (!$event->isError()) {
            return;
        }

        $exception = $event->getParam('exception');
        if (!$exception instanceof ChangePasswordException) {
            return;
        }

        $event->setResult(new ApiProblem(401, $exception->getMessage()));
        $event->setError(false);
    }

    /**
     * Short Circuits an API Problem Response if the user needs to change password
     *
     * @return void|ApiProblemResponse
     */
    public function onDispatch()
    {
        try {
            $this->getAuthenticationService()->getIdentity();
            return null;
        } catch (ChangePasswordException $changePassword) {

        }

        return new ApiProblemResponse(new ApiProblem(401, $changePassword->getMessage()));
    }
}
