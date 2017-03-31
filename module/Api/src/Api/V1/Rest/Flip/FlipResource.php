<?php

namespace Api\V1\Rest\Flip;

use Flip\Service\FlipServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * A Rest Resouce that deals with managing flips
 */
class FlipResource extends AbstractResourceListener
{
    /**
     * @var FlipServiceInterface
     */
    protected $flipService;

    /**
     * FlipResource constructor.
     * @param FlipServiceInterface $flipService
     */
    public function __construct(FlipServiceInterface $flipService)
    {
        $this->flipService = $flipService;
    }

    /**
     * @inheritdoc
     */
    public function fetch($flipId)
    {
        return $this->flipService->fetchFlipById($flipId, new FlipEntity());
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $params = (array) $params;
        unset($params['page'], $params['per_page']);
        return new FlipCollection(
            $this->flipService->fetchAll($params, new FlipEntity())
        );
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $flip = new FlipEntity($this->getInputFilter()->getValues());
        if ($this->flipService->createFlip($flip)) {
            return $flip;
        }

        return new ApiProblem(500, 'Unable to create flip');
    }

    /**
     * @inheritdoc
     */
    public function update($flipId, $data)
    {
        $flip = $this->fetch($flipId);
        $flip->exchangeArray($this->getInputFilter()->getValues());

        if ($this->flipService->updateFlip($flip)) {
            return $flip;
        }

        return new ApiProblem(500, 'Unable to update flip');
    }

    /**
     * @inheritdoc
     */
    public function delete($flipId)
    {
        $flip = $this->fetch($flipId);

        if ($this->flipService->deleteFlip($flip)) {
            return true;
        }

        return new ApiProblem(500, 'Unable to delete flip');
    }
}
