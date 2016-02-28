<?php
namespace Api\V1\Rest\User;

use User\Service\UserServiceInterface;
use User\StaticUserFactory;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class UserResource
 *
 * @package Api\V1\Rest\User
 */
class UserResource extends AbstractResourceListener
{
    /**
     * @var UserServiceInterface
     */
    protected $service;

    /**
     * UserResource constructor.
     * @param UserServiceInterface $service
     */
    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;
        unset($data['user_id']);
        $user = StaticUserFactory::createUser($data);

        $this->service->saveUser($user);
        return new UserEntity($user->getArrayCopy());
    }

    /**
     * Delete a resource
     *
     * @param  mixed $userId
     * @return ApiProblem|mixed
     */
    public function delete($userId)
    {
        $user = $this->fetch($userId);

        $this->service->deleteUser($user);
        return new ApiProblem(200, 'User deleted', 'Ok');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $userId
     * @return ApiProblem|mixed
     */
    public function fetch($userId)
    {
        return new UserEntity($this->service->fetchUser($userId)->getArrayCopy());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        /** @var DbSelect $users */
        $users = $this->service->fetchAll(null, true, new UserEntity());
        return new UserCollection($users);
    }

    /**
     * Update a resource
     *
     * @param  mixed $userId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($userId, $data)
    {
        $user = $this->fetch($userId);
        $data = $this->getInputFilter()->getValues();

        $data['user_id'] = $userId;
        foreach ($data as $key => $value) {
            $user->__set($key, $value);
        }

        $this->service->saveUser($user);
        return $user;
    }
}
