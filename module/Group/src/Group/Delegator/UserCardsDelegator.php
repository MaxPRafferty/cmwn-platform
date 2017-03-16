<?php


namespace Group\Delegator;


use Group\GroupInterface;
use Group\Service\UserCardService;
use Group\Service\UserCardServiceInterface;
use Zend\EventManager\EventManagerInterface;

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
        $this->service->generateUserCards($group);
    }
}
