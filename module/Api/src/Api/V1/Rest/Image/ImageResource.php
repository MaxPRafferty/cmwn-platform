<?php

namespace Api\V1\Rest\Image;

use Asset\Image;
use Asset\Service\ImageServiceInterface;
use Zend\Http\PhpEnvironment\Request;
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
     * @todo cloudinary service
     */
    public function create($data)
    {
        //check header
        /** @var Request $request */
        $request   = $this->getEvent()->getRequest();
        $content   = $request->getContent();
        $signature = $request->getHeader('X-Cld-Signature')->getFieldValue();
        $timestamp = $request->getHeader('X-Cld-Timestamp')->getFieldValue();
        $secret    = getenv('CLOUDINARY_API_SECRET');

        if (!$signature === sha1($content . $timestamp . $secret)) {
            return new ApiProblem(403, 'Not authorized');
        }
        
        $imageId = $this->getInputFilter()->getValue('public_id');
        $image   = $this->service->fetchImage($imageId);
        $code    = $image::statusToNumber($this->getInputFilter()->getValue('moderation_status'));

        $image->setModerated($code);
        $this->service->saveImage($image);
        return new ApiProblem(200, 'Ok');
    }
}
