<?php
namespace Api\V1\Rest\User;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use User\Service\UserServiceInterface;
use User\StaticUserFactory;
use User\UserInterface;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class UserResource
 */
class UserResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

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
        $data = $this->getInputFilter()->getValues();
        unset($data['user_id']);
        $user = StaticUserFactory::createUser($data);

        $this->service->createUser($user);

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
     * @return ApiProblem|UserEntity|UserInterface
     */
    public function fetch($userId)
    {
        $user         = $this->getEvent()->getRouteParam('user', false);
        try {
            $loggedInUser = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $loggedInUser = $changePassword->getUser();
        }

        if ($loggedInUser instanceof UserInterface && $loggedInUser->getUserId() === $user->getUserId()) {
            return new MeEntity($loggedInUser);
        }

        if ($user === false) {
            $user = $this->service->fetchUser($userId);
        }

        if ($user->getUserId() !== $userId) {
            return new ApiProblem(409, 'Loaded user does not match requested user');
        }

        return new UserEntity($user->getArrayCopy());
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

        $saveUser = StaticUserFactory::createUser($user->getArrayCopy());
        $this->service->updateUser($saveUser);
        return $user;
    }
}
