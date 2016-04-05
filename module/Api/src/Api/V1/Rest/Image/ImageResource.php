<?php

namespace Api\V1\Rest\Image;

use Asset\Image;
use Asset\Service\ImageServiceInterface;
use Zend\Http\Header\HeaderInterface;
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

        // TODO move to special guard
        $content   = $request->getContent();

        $sigHeader = $request->getHeader('X-Cld-Signature');
        $signature = $sigHeader instanceof HeaderInterface ? $sigHeader->getFieldValue() : '';

        $tsHeader  = $request->getHeader('X-Cld-Timestamp');
        $timestamp = $tsHeader instanceof HeaderInterface ? $tsHeader->getFieldValue() : '';

        $secret    = getenv('CLOUDINARY_API_SECRET');
        $check     = sha1($content . $timestamp . $secret);
        if ($signature !== $check) {
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
