<?php

namespace Api\V1\Rest\Ack;

use Flip\EarnedFlip;
use Flip\Service\FlipUserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Used to acknowledge the user has seen a flip
 */
class AckResource extends AbstractResourceListener
{
    /**
     * @var FlipUserServiceInterface
     */
    protected $flipUserService;

    /**
     * AckResource constructor.
     *
     * @param FlipUserServiceInterface $flipUserService
     */
    public function __construct(FlipUserServiceInterface $flipUserService)
    {
        $this->flipUserService = $flipUserService;
    }

    /**
     * @inheritDoc
     */
    public function update($ackId, $data)
    {
        $this->flipUserService->acknowledgeFlip(new EarnedFlip(['acknowledge_id' => $ackId]));
        return new ApiProblem(204, 'Acknowledged');
    }
}
