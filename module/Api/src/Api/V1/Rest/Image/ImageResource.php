<?php

namespace Api\V1\Rest\Image;

use Asset\Image;
use Asset\Service\ImageServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ImageResource
 * @package Api\V1\Rest\Image
 */
class ImageResource extends AbstractResourceListener
{
    /**
     * @var ImageServiceInterface
     */
    protected $service;

    /**
     * ImageResource constructor.
     * @param ImageServiceInterface $service
     */
    public function __construct(ImageServiceInterface $service)
    {
        $this->service = $service;
    }
    
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data  = (array) $data;
        $image = new Image($data);

        $this->service->saveNewImage($image);
        return new ImageEntity($image->getArrayCopy());
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $imageId
     * @return ApiProblem|mixed
     */
    public function fetch($imageId)
    {
        return new ImageEntity($this->service->fetchImage($imageId)->getArrayCopy());
    }
}
