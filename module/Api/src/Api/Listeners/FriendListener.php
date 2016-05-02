<?php

namespace Api\Listeners;

use Api\Links\FriendLink;
use Friend\FriendInterface;
use Friend\Service\FriendServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertions\DefaultAssertion;
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
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var array
     */
    protected $listeners = [];

    public function __construct(FriendServiceInterface $friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -649);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'onRender']);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach('Zend\Mvc\Application', $this->listeners[0]);
        $events->detach('ZF\Hal\Plugin\Hal', $this->listeners[1]);
        $events->detach('ZF\Rest\Controller', $this->listeners[2]);
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

        $event->setParam('assertion', new DefaultAssertion());
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

    /**
     * @param Event $event
     */
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
        if (!$realEntity instanceof FriendInterface) {
            return;
        }

        $payload = $event->getParam('payload');
        if ($event->getName() === 'renderEntity.post') {
            $payload['friend_status'] = $realEntity->getFriendStatus();
            return;
        }

        $status = $this->friendService->fetchFriendStatusForUser($authUser, $realEntity);
        $realEntity->setFriendStatus($status);

        if ($status === FriendInterface::FRIEND) {
            $entity->getLinks()->add(new FriendLink($authUser->getUserId(), $realEntity->getUserId()));
        }
    }
}
