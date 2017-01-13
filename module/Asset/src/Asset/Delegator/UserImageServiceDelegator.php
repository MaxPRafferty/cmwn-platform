<?php

namespace Asset\Delegator;

use Application\Utils\ServiceTrait;
use Asset\ImageInterface;
use Asset\Service\UserImageService;
use Asset\Service\UserImageServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserImageServiceDelegator
 */
class UserImageServiceDelegator implements UserImageServiceInterface
{
    use ServiceTrait;

    /**
     * @var array Adds the Importer interface the shared manager
     */
    protected $eventIdentifier = [UserImageServiceInterface::class];

    /**
     * @var UserImageService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * UserImageServiceDelegator constructor.
     * @param UserImageService $userImageService
     * @param EventManagerInterface $events
     */
    public function __construct(UserImageService $userImageService, EventManagerInterface $events)
    {
        $this->realService = $userImageService;
        $this->events = $events;
        $events->addIdentifiers(array_merge(
            [UserImageServiceInterface::class, static::class, UserImageService::class],
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
     * Saves an image to a user
     *
     * @param ImageInterface|string $image
     * @param string|UserInterface $user
     * @return bool
     * @throws \Exception
     */
    public function saveImageToUser($image, $user)
    {
        $eventParams = ['user' => $user, 'image' => $image];
        $event       = new Event('save.user.image', $this->realService, $eventParams);
        if ($this->getEventManager()->triggerEvent($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->saveImageToUser($image, $user);
            $event->setName('save.user.image.post');
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('save.user.image.error');
            throw $attachException;
        }

        $this->getEventManager()->triggerEvent($event);
        return $return;
    }

    /**
     * Fetches an image for a user
     *
     * @param $user
     * @param bool $approvedOnly
     * @return \Asset\Image|bool
     * @throws \Exception
     */
    public function fetchImageForUser($user, $approvedOnly = true)
    {
        $eventParams = ['user' => $user, 'approved_only' => $approvedOnly];
        $event       = new Event('fetch.user.image', $this->realService, $eventParams);
        if ($this->getEventManager()->triggerEvent($event)->stopped()) {
            return false;
        }

        $approvedOnly = $event->getParam('approved_only');

        try {
            $return = $this->realService->fetchImageForUser($user, $approvedOnly);
            $event->setName('fetch.user.image.post');
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('fetch.user.image.error');
            throw $attachException;
        }

        $this->getEventManager()->triggerEvent($event);
        return $return;
    }
}
