<?php

namespace Api\V1\Rest\Media;

use Media\Service\MediaServiceInterface;
use Zend\Http\Client\Adapter\Exception\TimeoutException;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Resource that talks to the media server
 */
class MediaResource extends AbstractResourceListener
{
    /**
     * @var MediaServiceInterface
     */
    protected $mediaService;

    /**
     * MediaResource constructor.
     *
     * @param MediaServiceInterface $mediaService
     */
    public function __construct(MediaServiceInterface $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function fetch($mediaId)
    {
        try {
            return $this->mediaService->importMediaData($mediaId, new MediaEntity());
        } catch (TimeoutException $timeout) {
            // TODO move to rules engine
        }

        return new ApiProblem(408, $timeout);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        try {
            return $this->mediaService->importMediaData('', new MediaEntity());
        } catch (TimeoutException $timeout) {
            // TODO move to rules engine
        }

        return new ApiProblem(408, $timeout);
    }
}
