<?php

namespace Api\Rule\Provider;

use Org\OrganizationInterface;
use Rule\Event\Provider\AbstractEventProvider;
use Security\Service\SecurityOrgServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\Hal\Entity;

/**
 * This provides relationship between active user and the Org given
 */
class ActiveUserOrgRoleProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = self::class;
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var SecurityOrgServiceInterface
     */
    protected $securityOrgService;

    /**
     * ActiveUserGroupRoleProvider constructor.
     * @param AuthenticationServiceInterface $authService
     * @param SecurityOrgServiceInterface $securityOrgService
     * @param string $providerName
     */
    public function __construct(
        AuthenticationServiceInterface $authService,
        SecurityOrgServiceInterface $securityOrgService,
        string $providerName = self::PROVIDER_NAME
    ) {
        parent::__construct($providerName);
        $this->securityOrgService = $securityOrgService;
        $this->authService = $authService;
    }

    /**@inheritdoc*/
    public function getValue()
    {
        $entity = $this->getEvent()->getParam('entity');

        if (!$entity instanceof Entity || !$entity->getEntity() instanceof OrganizationInterface) {
            return 'guest';
        }

        $org = $entity->getEntity();

        /**@var \Security\SecurityUser $authUser*/
        $authUser = $this->authService->getIdentity();

        if ($authUser->isSuper()) {
            return 'super';
        }

        return $this->securityOrgService->getRoleForOrg($org, $authUser);
    }
}
