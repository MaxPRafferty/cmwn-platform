<?php

namespace Api\V1\Rest\Flip;

use Flip\Flip;
use Flip\Service\FlipServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FlipResource
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
        return new FlipEntity($this->flipService->fetchFlipById($flipId)->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        return new FlipCollection($this->flipService->fetchAll(null, new FlipEntity()));
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $data = (array) $data;
        $flip = new Flip($data);
        $this->flipService->createFlip($flip);

        return new FlipEntity($flip->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $flip = $this->flipService->fetchFlipById($id);
        $flip->exchangeArray(array_merge($flip->getArrayCopy(), (array) $data));
        $this->flipService->updateFlip($flip);
        return new FlipEntity($flip->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function delete($flipId)
    {
        $flip = $this->flipService->fetchFlipById($flipId);

        $this->flipService->deleteFlip($flip);

        return new ApiProblem(200, 'flip deleted successfully');
    }
}
