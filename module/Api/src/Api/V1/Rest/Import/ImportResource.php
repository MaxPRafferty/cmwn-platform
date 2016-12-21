<?php
namespace Api\V1\Rest\Import;

use Group\GroupAwareInterface;
use Group\Service\GroupServiceInterface;
use Import\ImporterInterface;
use Interop\Container\ContainerInterface;
use Job\Service\JobServiceInterface;
use Notice\NotificationAwareInterface;
use User\UserInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ImportResource
 *
 * Triggers the job to import a file to a group
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportResource extends AbstractResourceListener
{
    /**
     * @var ContainerInterface
     */
    protected $services;

    /**
     * ImportResource constructor.
     *
     * @param ContainerInterface $services
     */
    public function __construct(ContainerInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Creates a new import job and sends it to the job service
     *
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $type = $this->getInputFilter()->getValue('type');
        if (!$this->services->has($type)) {
            return new ApiProblem(500, 'Invalid importer type');
        }

        $job = $this->services->get($type);
        if (!$job instanceof ImporterInterface) {
            return new ApiProblem(500, 'Not a valid importer');
        }

        $job->exchangeArray($this->getInputFilter()->getValues());

        if ($job instanceof NotificationAwareInterface) {
            $job->setEmail($this->getUser()->getEmail());
        }

        if ($job instanceof GroupAwareInterface) {
            $job->setGroup($this->getGroup());
        }

        $token = $this->getJobService()->sendJob($job);

        return new ImportEntity($token);
    }

    /**
     * Loads the group from the
     *
     * @return \Group\GroupInterface
     */
    protected function getGroup()
    {
        return $this->services
            ->get(GroupServiceInterface::class)
            ->fetchGroup($this->getEvent()->getRouteParam('group_id'));
    }

    /**
     * Gets the user from the authentication service
     *
     * @return UserInterface|null
     */
    protected function getUser()
    {
        /** @var AuthenticationServiceInterface $authService */
        $authService = $this->services->get(AuthenticationServiceInterface::class);
        if (!$authService->hasIdentity()) {
            return null;
        }

        // do not catch the change password here
        // we want to force the user to change their password
        return $authService->getIdentity();
    }

    /**
     * @return JobServiceInterface
     */
    protected function getJobService()
    {
        return $this->services->get(JobServiceInterface::class);
    }
}
