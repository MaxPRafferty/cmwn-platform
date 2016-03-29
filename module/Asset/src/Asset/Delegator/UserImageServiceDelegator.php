<?php

namespace Asset\Delegator;

use Asset\Service\UserImageService;
use Asset\Service\UserImageServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class UserImageServiceDelegator
 */
class UserImageServiceDelegator implements UserImageServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var UserImageService
     */
    protected $realService;

    /**
     * UserImageServiceDelegator constructor.
     * @param UserImageService $userImageService
     */
    public function __construct(UserImageService $userImageService)
    {
        $this->realService = $userImageService;
    }

    /**
     * @param $image
     * @param $user
     * @return bool
     */
    public function saveImageToUser($image, $user)
    {
        $eventParams = ['user' => $user, 'image' => $image];
        $event       = new Event('save.user.image', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->saveImageToUser($user, $image);
            $event->setName('save.user.image.post');
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('save.user.image.error');
            $return = false;
        }

        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param $user
     * @return \Asset\Image|bool
     */
    public function fetchImageForUser($user)
    {
        $eventParams = ['user' => $user];
        $event       = new Event('fetch.user.image', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->fetchImageForUser($user);
            $event->setName('fetch.user.image.post');
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event->setName('fetch.user.image.error');
            $return = false;
        }

        $this->getEventManager()->trigger($event);
        return $return;
    }
}
