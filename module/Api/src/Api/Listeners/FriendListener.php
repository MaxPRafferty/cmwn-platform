<?php

namespace Api\Listeners;

use Api\Links\FriendLink;
use Api\V1\Rest\User\UserEntity;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZF\ContentNegotiation\ParameterDataContainer;
use ZF\Hal\Entity;

/**
 * Class FriendRouteListener
 */
class FriendListener implements AuthenticationServiceAwareInterface
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
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -649);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach('Zend\Mvc\Application', $this->listeners[0]);
        $events->detach('ZF\Hal\Plugin\Hal', $this->listeners[1]);
    }

    /**
     * @param MvcEvent $event
     * @return null
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return null;
        }

        if ($event->getRouteMatch()->getMatchedRouteName() !== 'api.rest.friend') {
            return null;
        }

        if ($request->getMethod() !== HttpRequest::METHOD_POST) {
            return null;
        }

        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (! $dataContainer instanceof ParameterDataContainer) {
            return null;
        }

        $userId = $event->getRouteMatch()->getParam('user_id', false);
        $dataContainer->setBodyParam('user_id', $userId);
    }

    public function onRender(Event $event)
    {
        // Should never be able to load a scope object
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        /** @var UserInterface $authUser */
        $authUser = $this->getAuthenticationService()->getIdentity();
        if ($authUser->getType() !== UserInterface::TYPE_CHILD) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;
        if (!$realEntity instanceof UserEntity) {
            return;
        }

        if ($realEntity->getType() === UserInterface::TYPE_CHILD) {
            $realEntity->getLinks()->add(new FriendLink($authUser->getUserId(), $realEntity->getUserId()));
        }
    }
}
