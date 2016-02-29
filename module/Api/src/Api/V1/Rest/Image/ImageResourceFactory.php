<?php

namespace Api\V1\Rest\Image;

use Asset\Service\ImageServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImageResourceFactory
 * @package Api\V1\Rest\Image
 */
class ImageResourceFactory
{
    /**
     * @param ServiceLocatorInterface $services
     * @return ImageResource
     */
    public function __invoke(ServiceLocatorInterface $services)
    {
        /** @var ImageServiceInterface $imageService */
        $imageService = $services->get('Image\Service');
        return new ImageResource($imageService);
    }
}
