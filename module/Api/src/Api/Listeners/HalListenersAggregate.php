<?php

namespace Api\Listeners;

use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;

/**
 * Class HalListenersAggregate
 * @package Api\Listeners
 */
class HalListenersAggregate
{
    /**
     * @param SharedEventManagerInterface $manager
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        $manager->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'addUserLinks']);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {

    }

    public function addUserLinks(Event $event)
    {
        /** @var Entity $entity */
        $entity = $event->getParam('entity');
        // TODO Re-implement once this route is back
        // TODO Make this listener load other listeners from SM
        return;
        if (!$entity->entity instanceof UserInterface) {
        }

        /** @var UserInterface $user */
        $user = $entity->entity;
        $entity->getLinks()->add(Link::factory([
            'rel' => 'cmwn:reset.password',
            'route' => [
                'name' => 'api.rest.user-password',
                'params' => ['user_id' => $user->getUserId()]
            ]
        ]));
    }
}
