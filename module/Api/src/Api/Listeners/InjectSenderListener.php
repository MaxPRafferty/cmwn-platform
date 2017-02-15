<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use Feed\FeedInterface;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class InjectSenderListener
 * @package Api\Listeners
 */
class InjectSenderListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * InjectSenderListener constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach($listener, 'ZF\Hal\Plugin\Hal');
        }
    }

    /**
     * @param Event $event
     */
    public function onRender(Event $event)
    {
        $entity = $event->getParam('entity');

        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->getEntity();

        if (!$realEntity instanceof FeedInterface) {
            return;
        }

        $sender = $realEntity->getSender();

        if ($sender === null || $sender instanceof UserInterface) {
            return;
        }

        $payLoad = $event->getParam('payload');

        if (!isset($payLoad['sender'])) {
            return;
        }

        try {
            $sender = $this->userService->fetchUser($sender, null);
            $payLoad['sender'] = $sender->getArrayCopy();
        } catch (NotFoundException $nf) {
            throw new \InvalidArgumentException('invalid sender');
        }
    }
}
