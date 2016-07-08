<?php

namespace Friend\Delegator;

use Application\Utils\ServiceTrait;
use Friend\Service\SuggestedFriendService;
use Friend\Service\SuggestedFriendServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class SuggestedFriendServiceDelegator
 */
class SuggestedFriendServiceDelegator implements SuggestedFriendServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var SuggestedFriendService
     */
    protected $realService;

    /**
     * SuggestedFriendServiceDelegator constructor.
     * @param SuggestedFriendService $realService
     */
    public function __construct(SuggestedFriendService $realService)
    {
        $this->realService = $realService;
    }

    /**
     * Fetches the suggested users for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @throws \Exception
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null)
    {
        $where       = $this->createWhere($where);
        $eventParams = ['user' => $user, 'where' => $where, 'prototype' => $prototype];
        $event       = new Event('fetch.suggested.friends', $this->realService, $eventParams);
        $response    = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchSuggestedFriends($user, $where, $prototype);
            $event->setName('fetch.suggested.friends.post');
        } catch (\Exception $exception) {
            $event->setParam('exception', $exception);
            $event->setName('fetch.suggested.friends.error');
            $this->getEventManager()->trigger($event);
            throw $exception;
        }

        $this->getEventManager()->trigger($event);
        return $return;
    }
}
