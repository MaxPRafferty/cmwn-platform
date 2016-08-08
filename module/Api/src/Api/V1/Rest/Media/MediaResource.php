<?php

namespace Api\V1\Rest\Media;

use Media\Service\MediaServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class MediaResource
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
     * Fetch a resource
     *
     * @param  mixed $mediaId
     * @return ApiProblem|mixed
     */
    public function fetch($mediaId)
    {
        return $this->mediaService->importMediaData($mediaId, new MediaEntity());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return $this->mediaService->importMediaData('', new MediaEntity());
    }
}
