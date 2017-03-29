<?php

namespace Group\Delegator;

use Group\GroupInterface;
use Group\Service\UserCardService;
use Group\Service\UserCardServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;

/**
 * Delegator for user cards service
 */
class UserCardsDelegator implements UserCardServiceInterface
{
    /**
     * @var UserCardServiceInterface
     */
    protected $service;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * UserCardsDelegator constructor.
     * @param UserCardServiceInterface $userCardService
     * @param EventManagerInterface $events
     */
    public function __construct(UserCardServiceInterface $userCardService, EventManagerInterface $events)
    {
        $this->service = $userCardService;
        $this->events  = $events;
        $events->addIdentifiers(array_merge(
            [UserCardServiceInterface::class, static::class, UserCardService::class],
            $events->getIdentifiers()
        ));
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function generateUserCards(GroupInterface $group)
    {
        $eventParams = ['group' => $group];
        $event       = new Event('fetch.group.user-cards', $this->service, $eventParams);
        $response    = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->service->generateUserCards($group);
        } catch (\Exception $fetchException) {
            $event->setParam('exception', $fetchException);
            $event->setName('fetch.group.user-cards.error');
            $this->getEventManager()->triggerEvent($event);
            throw $fetchException;
        }

        $event->setName('fetch.group.user-cards.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
