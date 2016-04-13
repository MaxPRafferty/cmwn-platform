<?php
namespace Api\V1\Rest\Import;

use Group\GroupAwareInterface;
use Import\ImporterInterface;
use Job\Service\JobServiceInterface;
use Notice\NotificationAwareInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ImportResource
 */
class ImportResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    public function __construct(JobServiceInterface $jobService, ServiceLocatorInterface $services)
    {
        $this->jobService = $jobService;
        $this->services   = $services;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $type = $this->getInputFilter()->getValue('type');
        $job  = $this->services->get($type);

        if (!$job instanceof ImporterInterface) {
            return new ApiProblem(500, 'Invalid importer type');
        }

        $job->exchangeArray($this->getInputFilter()->getValues());

        $user = $this->getUser();
        if ($job instanceof NotificationAwareInterface && $user instanceof SecurityUser) {
            $job->setEmail($user->getEmail());
        }

        if ($job instanceof GroupAwareInterface) {
            $job->setGroup($this->getEvent()->getRouteParam('group'));
        }

        $token = $this->jobService->sendJob($job);
        return new ImportEntity($token);
    }

    protected function getUser()
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return null;
        }

        try {
            $identity = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $identity = $changePassword->getUser();
        }

        return $identity;
    }
}
