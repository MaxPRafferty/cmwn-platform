<?php

namespace Security\Authentication;

use Security\SecurityUser;
use Security\Service\SecurityOrgService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use ZF\MvcAuth\Authentication\AdapterInterface as ZfApiAdapter;
use ZF\MvcAuth\Identity\AuthenticatedIdentity;
use ZF\MvcAuth\Identity\GuestIdentity;
use ZF\MvcAuth\Identity\IdentityInterface;
use ZF\MvcAuth\MvcAuthEvent;

/**
 * Class CmwnAuthenticationAdapter
 * @package Security\Authentication
 */
class ApiAdapter implements ZfApiAdapter
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $service;

    /**
     * @var SecurityOrgService
     */
    protected $securityOrgService;

    /**
     * ApiAdapter constructor.
     * @param AuthenticationServiceInterface $service
     * @param SecurityOrgService $securityOrgService
     */
    public function __construct(AuthenticationServiceInterface $service, SecurityOrgService $securityOrgService)
    {
        $this->service            = $service;
        $this->securityOrgService = $securityOrgService;
    }

    /**
     * @return array Array of types this adapter can handle.
     */
    public function provides()
    {
        return ['user'];
    }

    /**
     * Attempt to match a requested authentication type
     * against what the adapter provides.
     *
     * @param string $type
     * @return bool
     */
    public function matches($type)
    {
        return true;
    }

    /**
     * Attempt to retrieve the authentication type based on the request.
     *
     * Allows an adapter to have custom logic for detecting if a request
     * might be providing credentials it's interested in.
     *
     * @param Request $request
     * @return false|string
     */
    public function getTypeFromRequest(Request $request)
    {
        return 'user';
    }

    /**
     * Perform pre-flight authentication operations.
     *
     * Use case would be for providing authentication challenge headers.
     *
     * @param Request $request
     * @param Response $response
     * @return void|Response
     */
    public function preAuth(Request $request, Response $response)
    {
        // noop
    }

    /**
     * Attempt to authenticate the current request.
     *
     * @param Request $request
     * @param Response $response
     * @param MvcAuthEvent $mvcAuthEvent
     * @return false|IdentityInterface False on failure, IdentityInterface
     *     otherwise
     */
    public function authenticate(Request $request, Response $response, MvcAuthEvent $mvcAuthEvent)
    {
        if (!$this->service->hasIdentity()) {
            return null;
        }

        $identity = $this->service->getIdentity();
        if ($identity instanceof GuestIdentity) {
            return $identity;
        }

        if ($identity instanceof AuthenticatedIdentity) {
            $identity->setName(
                $this->resolveCurrentRoleForUser($mvcAuthEvent, $identity->getAuthenticationIdentity())
            );
            return $identity;
        }

        $user = $this->service->getIdentity();
        $identity = new AuthenticatedIdentity($user);
        $identity->setName($this->resolveRoleForUser($mvcAuthEvent, $user));
        return $identity;
    }

    protected function resolveCurrentRoleForUser(MvcAuthEvent $mvcAuthEvent, SecurityUser $securityUser)
    {
        if ($securityUser->isSuper()) {
            return 'super';
        }

        $groupId = $mvcAuthEvent->getMvcEvent()->getRouteMatch()->getParam('group_id', false);
        if ($groupId === false) {
            return 'logged_in';
        }

        $role = $this->securityOrgService->getRoleForGroup($groupId, $securityUser);

        return 'logged_in';
    }
}
