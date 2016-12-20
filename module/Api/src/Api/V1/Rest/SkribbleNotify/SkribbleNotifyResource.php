<?php

namespace Api\V1\Rest\SkribbleNotify;

use Api\V1\Rest\Skribble\SkribbleEntity;
use Skribble\Service\SkribbleServiceInterface;
use Skribble\SkribbleInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SkribbleNotifyResource
 */
class SkribbleNotifyResource extends AbstractResourceListener
{
    /**
     * @var SkribbleServiceInterface
     */
    protected $skribbleService;

    /**
     * SkribbleNotifyResource constructor.
     *
     * @param SkribbleServiceInterface $skribbleService
     */
    public function __construct(SkribbleServiceInterface $skribbleService)
    {
        $this->skribbleService = $skribbleService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     *
     * @return ApiProblem|SkribbleEntity|SkribbleInterface
     */
    public function create($data)
    {
        $skribbleId = $this->getEvent()->getRouteParam('skribble_id', false);
        $skribble   = $this->skribbleService->fetchSkribble($skribbleId, new SkribbleEntity());

        $mapStatus = [
            'error'   => SkribbleInterface::STATUS_ERROR,
            'success' => SkribbleInterface::STATUS_COMPLETE,
        ];

        $status = $this->getInputFilter()->getValue('status');

        $skribbleStatus = isset($mapStatus[$status]) ? $mapStatus[$status] : false;

        // Just in case something went wrong with the config
        // @codeCoverageIgnoreStart
        if ($skribbleStatus === false) {
            return new ApiProblem(422, 'Invalid status');
        }
        // @codeCoverageIgnoreEnd

        $skribble->setStatus($skribbleStatus);
        $this->skribbleService->updateSkribble($skribble);

        return $skribble;
    }
}
