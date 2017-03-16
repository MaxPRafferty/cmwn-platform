<?php
namespace Api\V1\Rest\User;

use Security\Exception\ChangePasswordException;
use User\Service\UserServiceInterface;
use User\UserHydrator;
use User\UserInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * The Resource for dealing with users
 */
class UserResource extends AbstractResourceListener
{
    /**
     * @var UserServiceInterface
     */
    protected $service;

    /**
     * @var UserHydrator
     */
    protected $hydrator;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * UserResource constructor.
     *
     * @param UserServiceInterface $service
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(UserServiceInterface $service, AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
        $this->service     = $service;
        $this->hydrator    = new UserHydrator();
    }

    /**
     * Create a new user
     *
     * The Authenticated user needs permission to create a user in order to create a new user
     *
     * @SWG\Post(path="/user",
     *   tags={"user"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="User data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/UserEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $data
     *
     * @return UserEntity|UserInterface
     */
    public function create($data)
    {
        $data = $this->getInputFilter()->getValues();
        unset($data['user_id']);
        $user = $this->hydrator->hydrate($data, new UserEntity());
        $this->service->createUser($user);

        return $user;
    }

    /**
     * Delete a user
     *
     * The user is fetched first to ensure the authenticated user can access the user to delete.  By default users are
     * soft deleted unless the "hard" query parameter is set.  The authenticated user needs permission to hard delete
     * users
     *
     * @SWG\Delete(path="/user/{user_id}",
     *   tags={"user"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="hard",
     *     in="query",
     *     description="Hard delete the user",
     *     type="boolean",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="User was deleted"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access user",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $userId
     *
     * @return ApiProblem|mixed
     */
    public function delete($userId)
    {
        $user = $this->fetch($userId);
        if ($this->service->deleteUser($user)) {
            return true;
        }

        return new ApiProblem(500, 'Failed to delete user');
    }

    /**
     * Fetch a user
     *
     * If the authenticated user is not allowed access, than a 403 is thrown.
     *
     * @SWG\Get(path="/user/{user_id}",
     *   tags={"user"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The User",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Items(ref="#/definitions/UserEntity")
     *     )
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not allowed to access this user",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     *
     * @param  string $userId
     *
     * @return ApiProblem|UserEntity|UserInterface
     */
    public function fetch($userId)
    {
        $user = $this->service->fetchUser($userId, new UserEntity());
        try {
            $loggedInUser = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $loggedInUser = $changePassword->getUser();
        }

        if ($loggedInUser instanceof UserInterface && $loggedInUser->getUserId() === $user->getUserId()) {
            return new MeEntity($loggedInUser);
        }

        return $user;
    }

    /**
     * Fetches multiple users that the authenticated user can access
     *
     * If the user is not allowed to list users a 403 is returned
     *
     * @SWG\Get(path="/user",
     *   tags={"user"},
     *   x={"prime-for":"user"},
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of user to fetch",
     *     type="string",
     *     enum={"CHILD","ADULT"},
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="deleted",
     *     in="query",
     *     description="Fetch deleted users",
     *     type="boolean",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to fetch",
     *     type="integer",
     *     format="int32",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of users on each page",
     *     type="integer",
     *     format="int32",
     *     minimum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged users",
     *     @SWG\Schema(ref="#/definitions/UserCollection")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User not found",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $params = (array)$params;
        // TODO Provide a better way to remove these parameters using ZF\Rest\Controller options
        unset($params['page']);
        unset($params['per_page']);

        return new UserCollection($this->service->fetchAll($params, new UserEntity()));
    }

    /**
     * Updates a user
     *
     * The user to be updated is fetched first to ensure the user has access to edit the user.  All valid data for the
     * user is needed.
     *
     * @SWG\Put(path="/user/{user_id}",
     *   tags={"user"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="User Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     minimum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="User data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/User")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/UserEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update or access this user",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     *
     * @param  string $userId
     * @param  array $data
     *
     * @return ApiProblem|mixed
     */
    public function update($userId, $data)
    {
        $user = $this->fetch($userId);
        $data = $this->getInputFilter()->getValues();

        foreach ($data as $key => $value) {
            $user->__set($key, $value);
        }

        $this->service->updateUser($user);

        return $user;
    }
}
