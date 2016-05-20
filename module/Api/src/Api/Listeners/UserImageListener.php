<?php

namespace Api\Listeners;

use Api\V1\Rest\Image\ImageEntity;
use Api\V1\Rest\User\MeEntity;
use Api\V1\Rest\User\UserEntity;
use Asset\Service\UserImageServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Plugin\Hal;

/**
 * Class UserImageListener
 *
 * Injects the user image into User payload
 */
class UserImageListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var UserImageServiceInterface
     */
    protected $userImageService;

    /**
     * UserGroupListener constructor.
     *
     * @param UserImageServiceInterface $userImageService
     */
    public function __construct(UserImageServiceInterface $userImageService)
    {
        $this->userImageService = $userImageService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Hal::class, 'renderEntity.post', [$this, 'attachImage'], -1000);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach(Hal::class, $listener);
        }
    }

    /**
     * @param Event $event
     */
    public function attachImage(Event $event)
    {
        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof UserEntity) {
            return;
        }

        $payload = $event->getParam('payload');

        /** @var \ZF\Hal\Plugin\Hal $hal */
        $hal     = $event->getTarget();
        try {
            $image = $this->userImageService->fetchImageForUser($realEntity, !$realEntity instanceof MeEntity);
        } catch (\Exception $imageException) {
            return;
        }

        if ($image !== false) {
            $entityToRender = new Entity(new ImageEntity($image->getArrayCopy()));
            $payload['_embedded']['image'] = $hal->renderEntity($entityToRender);
        }
    }
}
