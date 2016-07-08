<?php

namespace Api\V1\Rest\Skribble;

use Skribble\Service\SkribbleServiceInterface;
use Skribble\Skribble;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SkribbleResource
 */
class SkribbleResource extends AbstractResourceListener
{
    /**
     * @var SkribbleServiceInterface
     */
    protected $service;

    /**
     * SkribbleResource constructor.
     *
     * @param SkribbleServiceInterface $service
     */
    public function __construct(SkribbleServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $skribble = new Skribble($this->getInputFilter()->getValues());

        /** @var UserInterface $created */
        $created = $this->getEvent()->getRouteParam('user');

        $skribble->setCreatedBy($created);
        $this->service->createSkribble($skribble);

        // Return back something the API can use
        return new SkribbleEntity($skribble->getArrayCopy());
    }

    /**
     * Delete a resource
     *
     * @param  mixed $skribbleId
     *
     * @return ApiProblem|mixed
     */
    public function delete($skribbleId)
    {
        $skribble = $this->fetch($skribbleId);
        $this->service->deleteSkribble($skribble);

        return new ApiProblem(200, 'Skribble Deleted', 'Ok');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $skribbleId
     *
     * @return ApiProblem|SkribbleEntity
     */
    public function fetch($skribbleId)
    {
        return $this->service->fetchSkribble($skribbleId, new SkribbleEntity());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     *
     * @return ApiProblem|SkribbleCollection
     */
    public function fetchAll($params = [])
    {
        $user = $this->getEvent()->getRouteParam('user');
        $type = isset($params['type']) ? $params['type'] : 'all';

        switch ($type) {
            case 'all':
                $adapter = $this->service->fetchAllForUser($user, null, new SkribbleEntity());
                break;

            case 'sent':
                $adapter = $this->service->fetchSentForUser($user, null, new SkribbleEntity());
                break;

            case 'received':
                $adapter = $this->service->fetchReceivedForUser($user, null, new SkribbleEntity());
                break;

            case 'draft':
                $adapter = $this->service->fetchDraftForUser($user, null, new SkribbleEntity());
                break;

            default:
                return new ApiProblem(406, sprintf('Cannot Query for type "%s"', $type));
        }

        return new SkribbleCollection($adapter);
    }

    /**
     * Update a resource
     *
     * @param  mixed $skribbleId
     * @param  mixed $data
     *
     * @return ApiProblem|SkribbleEntity
     */
    public function update($skribbleId, $data)
    {
        $skribbleEntity = $this->fetch($skribbleId);
        $skribbleEntity->exchangeArray($this->getInputFilter()->getValues());

        // Only save Skribbles to the DB not Skribble Entities
        $skribble = new Skribble($skribbleEntity->getArrayCopy());
        $this->service->updateSkribble($skribble);

        return $skribbleEntity;
    }
}
