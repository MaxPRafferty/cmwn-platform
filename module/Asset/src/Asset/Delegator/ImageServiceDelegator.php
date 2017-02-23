<?php

namespace Asset\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Asset\Service\ImageService;
use Asset\Service\ImageServiceInterface;
use Asset\ImageInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class ImageServiceDelegator
 * @package Asset\Delegator
 */
class ImageServiceDelegator implements ImageServiceInterface
{
    use ServiceTrait;

    /**
     * @var ImageService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * ImageServiceDelegator constructor.
     * @param ImageServiceInterface $service
     * @param EventManagerInterface $events
     */
    public function __construct(ImageServiceInterface $service, EventManagerInterface $events)
    {
        $this->realService = $service;
        $this->events = $events;
        $events->addIdentifiers(array_merge(
            [ImageServiceInterface::class, static::class, ImageService::class],
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
     * Attaches the HideDeleteEntitiesListener
     */
    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.images'], ['fetch.image.post']);
        $hideListener->setEntityParamKey('image');
        $hideListener->attach($this->getEventManager());
    }

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveNewImage(ImageInterface $image)
    {
        $event    = new Event('save.new.image', $this->realService, ['image' => $image]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveNewImage($image);

        $event    = new Event('save.new.image.post', $this->realService, ['image' => $image]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveImage(ImageInterface $image)
    {
        $event    = new Event('save.image', $this->realService, ['image' => $image]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveImage($image);

        $event    = new Event('save.image.post', $this->realService, ['image' => $image]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetches one image from the DB using the id
     *
     * @param $imageId
     * @return ImageInterface
     * @throws NotFoundException
     */
    public function fetchImage($imageId)
    {
        $event    = new Event('fetch.image', $this->realService, ['image_id' => $imageId]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchImage($imageId);
        $event    = new Event('fetch.image.post', $this->realService, ['image_id' => $imageId, 'image' => $return]);
        $this->getEventManager()->triggerEvent($event);
        return $return;
    }

    /**
     * Deletes a image from the database
     *
     * Soft deletes unless soft is false
     *
     * @param ImageInterface $image
     * @param bool $soft
     * @return bool
     */
    public function deleteImage(ImageInterface $image, $soft = true)
    {
        $event    = new Event('delete.image', $this->realService, ['image' => $image, 'soft' => $soft]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteImage($image, $soft);
        $event  = new Event('delete.image.post', $this->realService, ['image' => $image, 'soft' => $soft]);
        $this->getEventManager()->triggerEvent($event);
        return $return;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where    = $this->createWhere($where);
        $event    = new Event(
            'fetch.all.images',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $paginate, $prototype);
        $event    = new Event(
            'fetch.all.images.post',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype, 'images' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
