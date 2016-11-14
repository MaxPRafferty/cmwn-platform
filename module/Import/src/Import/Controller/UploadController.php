<?php

namespace Import\Controller;

use Group\GroupAwareInterface;
use Group\Service\GroupServiceInterface;
use Import\View\ImportView;
use Job\Service\JobServiceInterface;
use Import\ImporterInterface;
use Notice\NotificationAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\InputFilter\InputFilterAwareTrait;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UploadController
 * @method \Zend\Http\PhpEnvironment\Request getRequest()
 * @method \Zend\Http\PhpEnvironment\Response getResponse()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UploadController extends AbstractActionController
{
    use InputFilterAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * UploadController constructor.
     *
     * @param InputFilterInterface $importFilter
     * @param ServiceLocatorInterface $services
     * @param JobServiceInterface $jobService
     * @param GroupServiceInterface $groupService
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function __construct(
        InputFilterInterface $importFilter,
        ServiceLocatorInterface $services,
        JobServiceInterface $jobService,
        GroupServiceInterface $groupService,
        AuthenticationServiceInterface $authenticationService
    ) {
        $this->setInputFilter($importFilter);
        // TODO zf3 fix
        $this->setServiceLocator($services);
        $this->groupService = $groupService;
        $this->jobService = $jobService;
        $this->setAuthenticationService($authenticationService);
    }

    /**
     * @return ImportView|\Zend\Http\PhpEnvironment\Response
     */
    public function indexAction()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $response->setStatusCode(405);
            return $response;
        }

        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->getInputFilter()->setData($post);

        if (!$this->getInputFilter()->isValid()) {
            return new ImportView(['messages' => $this->getInputFilter()->getMessages()], true);
        }

        $type = $this->getInputFilter()->getValue('type');
        $job  = $this->getServiceLocator()->get($type);

        if (!$job instanceof ImporterInterface) {
            return new ImportView(['messages' => 'Invalid import type']);
        }

        $job->exchangeArray($this->getInputFilter()->getValues());

        $user = $this->getUser();
        if ($job instanceof NotificationAwareInterface && $user instanceof SecurityUser) {
            $job->setEmail($user->getEmail());
        }

        if ($job instanceof GroupAwareInterface) {
            $job->setGroup(
                $this->getGroup(
                    $this->getEvent()->getRouteMatch()->getParam('group_id', false)
                )
            );
        }

        $this->jobService->sendJob($job);
        return new ImportView([]);
    }


    /**
     * @param $groupId
     *
     * @return \Group\GroupInterface
     */
    protected function getGroup($groupId)
    {
        /** @var GroupServiceInterface $groupService */
        $groupService = $this->getServiceLocator()->get(GroupServiceInterface::class);
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
