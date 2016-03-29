<?php
namespace Api\V1\Rest\UserImage;

use Api\V1\Rest\Image\ImageEntity;
use Asset\Service\ImageServiceInterface;
use Asset\Service\UserImageServiceInterface;
use User\UserInterface;
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
    protected $imageService;

    /**
     * @var UserImageServiceInterface
     */
    protected $userImageService;

    /**
     * UserImageResource constructor.
     * @param ImageServiceInterface $service
     * @param UserImageServiceInterface $userImageService
     */
    public function __construct(ImageServiceInterface $service, UserImageServiceInterface $userImageService)
    {
        $this->imageService     = $service;
        $this->userImageService = $userImageService;
    }
    
    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;
        /** @var UserInterface $user */
        $user = $this->getEvent()->getRouteParam('user');

        $image = new ImageEntity();
        $image->setUrl($data['url']);
        $image->setImageId($data['image_id']);

        $this->imageService->saveImage($image);
        $this->userImageService->saveImageToUser($image, $user);

        return $image;
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
