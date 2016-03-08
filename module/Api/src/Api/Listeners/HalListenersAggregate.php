<?php

namespace Api\Listeners;

use Api\Links\ForgotLink;
use Api\Links\ProfileLink;
use Api\Links\UserImageLink;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class HalListenersAggregate
 * @package Api\Listeners
 */
class HalListenersAggregate
{
    protected $listeners = [];
    
    /**
     * @param SharedEventManagerInterface $manager
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        $this->listeners[] = $manager->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'addUserLinks']);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    /**
     * @param Event $event
     */
    public function addUserLinks(Event $event)
    {
        /** @var Entity $entity */
        $entity = $event->getParam('entity');
        // TODO Make this listener load other listeners from SM
        if (!$entity->entity instanceof UserInterface) {
            return;
        }

        /** @var UserInterface $user */
        $user = $entity->entity;
        $entity->getLinks()->add(new ForgotLink()); // TODO Change this to Reset Link
        $entity->getLinks()->add(new ProfileLink($user));
        $entity->getLinks()->add(new UserImageLink($user));
    }
}
