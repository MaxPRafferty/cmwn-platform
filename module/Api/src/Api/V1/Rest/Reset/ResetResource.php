<?php
namespace Api\V1\Rest\Reset;

use Forgot\Service\ForgotServiceInterface;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ResetResource
 */
class ResetResource extends AbstractResourceListener
{
    /**
     * @var ForgotServiceInterface
     */
    protected $forgotService;

    /**
     * ResetResource constructor.
     *
     * @param ForgotServiceInterface $forgotService
     */
    public function __construct(ForgotServiceInterface $forgotService)
    {
        $this->forgotService = $forgotService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        /** @var UserInterface $user */
        $user = $this->getEvent()->getRouteParam('user');
        $code = $this->getInputFilter()->getValue('code');
        $this->forgotService->saveForgotPassword($user->getUserName(), $code);
        return new ApiProblem(201, 'Ok');
    }
}
