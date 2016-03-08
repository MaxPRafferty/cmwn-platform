<?php

namespace Security\Authentication;

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
     * ApiAdapter constructor.
     * @param AuthenticationServiceInterface $service
     */
    public function __construct(AuthenticationServiceInterface $service)
    {
        $this->service = $service;
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
        return in_array($type, $this->provides(), true);
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
            return new GuestIdentity();
        }

        return new AuthenticatedIdentity($this->service->getIdentity());
    }
}
