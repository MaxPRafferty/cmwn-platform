<?php
namespace Api\V1\Rest\UserImage;

use Asset\Service\ImageServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class UserImageResource
 */
class UserImageResource extends AbstractResourceListener
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
        
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $userId
     * @return ApiProblem|mixed
     */
    public function fetch($userId)
    {
        return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }
}
