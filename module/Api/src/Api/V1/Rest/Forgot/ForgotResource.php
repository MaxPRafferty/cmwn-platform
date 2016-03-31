<?php

namespace Api\V1\Rest\Forgot;

use Forgot\Service\ForgotServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ForgotResource
 */
class ForgotResource extends AbstractResourceListener
{
    /**
     * @var ForgotServiceInterface
     */
    protected $forgotService;

    /**
     * ForgotResource constructor.
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
        $data  = (array) $data;
        $email = $data['email'];
        $this->forgotService->saveForgotPassword($email);
        return new ApiProblem(200, 'Ok');
    }
}
