<?php

namespace Api\Listeners;

use Api\Links\FriendLink;
use Api\Links\SuggestLink;
use Api\V1\Rest\User\MeEntity;
use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertions\DefaultAssertion;
use Suggest\NotFoundException;
use Suggest\Service\SuggestedService;
use Suggest\Service\SuggestedServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZF\ContentNegotiation\ParameterDataContainer;
use ZF\Hal\Entity;

/**
 * Class FriendRouteListener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FriendListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var SuggestedService
     */
    protected $suggestedService;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * FriendListener constructor.
     *
     * @param FriendServiceInterface $friendService
     * @param SuggestedServiceInterface $suggestedService
     */
    public function __construct(FriendServiceInterface $friendService, SuggestedServiceInterface $suggestedService)
    {
        $this->friendService = $friendService;
        $this->suggestedService = $suggestedService;
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
        $events->detach($this->listeners[0], 'Zend\Mvc\Application');
        $events->detach($this->listeners[1], 'ZF\Hal\Plugin\Hal');
        $events->detach($this->listeners[2], 'ZF\Hal\Plugin\Hal');
    }

    /**
     * @param MvcEvent $event
     *
     * @return null
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return null;
        }

        if (!in_array($event->getRouteMatch()->getMatchedRouteName(), ['api.rest.friend', 'api.rest.suggest'])) {
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
        return null;
    }

    /**
     * @param Event $event
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function onRender(Event $event)
    {
        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->getEntity();
        if (!$realEntity instanceof FriendInterface) {
            return;
        }


        // Should never be able to load a scope object
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        /** @var UserInterface $authUser */
        $authUser = $this->getAuthenticationService()->getIdentity();
        if ($authUser->getType() !== UserInterface::TYPE_CHILD) {
            return;
        }

        $payload = $event->getParam('payload');
        if ($event->getName() === 'renderEntity.post') {
            $payload['friend_status'] = $realEntity->getFriendStatus();
            return;
        }

        try {
            $status = $this->friendService->fetchFriendStatusForUser($authUser, $realEntity);
        } catch (NotFriendsException $nf) {
            try {
                $this->suggestedService->fetchSuggestedFriendForUser($authUser, $realEntity);
                $status = FriendInterface::CAN_FRIEND;
            } catch (NotFoundException $nf) {
                $status = FriendInterface::CANT_FRIEND;
            }
        }

        $realEntity->setFriendStatus($status);

        if ($realEntity instanceof MeEntity && $realEntity->getType() === UserInterface::TYPE_CHILD) {
            $entity->getLinks()->add(new SuggestLink($authUser->getUserId()));
        }

        if ($realEntity instanceof MeEntity) {
            return;
        }

        if ($status === FriendInterface::FRIEND && !$entity->getLinks()->has('friend')) {
            $entity->getLinks()->add(new FriendLink($authUser->getUserId(), $realEntity->getUserId()));
        }
    }
}
