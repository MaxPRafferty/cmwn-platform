<?php
namespace Api\V1\Rest\Import;

use Group\GroupAwareInterface;
use Group\Service\GroupServiceInterface;
use Import\ImporterInterface;
use Interop\Container\ContainerInterface;
use Job\Service\JobServiceInterface;
use Notice\NotificationAwareInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ImportResource
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @deprecated
 */
class ImportResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * @var ContainerInterface
     */
    protected $services;

    /**
     * ImportResource constructor.
     *
     * @param JobServiceInterface $jobService
     * @param ContainerInterface $services
     */
    public function __construct(JobServiceInterface $jobService, ContainerInterface $services)
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
            $job->setGroup(
                $this->getGroup(
                    $this->getEvent()->getRouteParam('group_id')
                )
            );
        }

        $token = $this->jobService->sendJob($job);
        return new ImportEntity($token);
    }

    /**
     * @param $groupId
     *
     * @return \Group\GroupInterface
     */
    protected function getGroup($groupId)
    {
        /** @var GroupServiceInterface $groupService */
        $groupService = $this->services->get(GroupServiceInterface::class);
        return $groupService->fetchGroup($groupId);
    }

    /**
     * @return mixed|null|\Security\ChangePasswordUser
     */
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
