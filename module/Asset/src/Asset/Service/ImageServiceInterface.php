<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Asset\ImageInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface ImageServiceInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface ImageServiceInterface
{

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null);

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveImage(ImageInterface $image);

    /**
     * Fetches one image from the DB using the id
     *
     * @param $imageId
     * @return ImageInterface
     * @throws NotFoundException
     */
    public function fetchImage($imageId);

    /**
     * Deletes a image from the database
     *
     * Soft deletes unless soft is false
     *
     * @param ImageInterface $image
     * @param bool $soft
     * @return bool
     */
    public function deleteImage(ImageInterface $image, $soft = true);

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveNewImage(ImageInterface $image);
}
