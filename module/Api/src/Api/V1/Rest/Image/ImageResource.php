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
     * Delete a resource
     *
     * @param  mixed $imageId
     * @return ApiProblem|mixed
     */
    public function delete($imageId)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
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

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $imageId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($imageId, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $imageId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($imageId, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
