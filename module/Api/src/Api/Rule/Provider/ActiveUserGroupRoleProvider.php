<?php

namespace Api\Rule\Provider;

use Group\GroupInterface;
use Rule\Event\Provider\AbstractEventProvider;
use Security\Service\SecurityGroupServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\Hal\Entity;

/**
 * This provides relationship between active user and the group given
 */
class ActiveUserGroupRoleProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = self::class;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var SecurityGroupServiceInterface
     */
    protected $securityGroupService;

    /**
     * ActiveUserGroupRoleProvider constructor.
     * @param AuthenticationServiceInterface $authService
     * @param SecurityGroupServiceInterface $securityGroupService
     * @param string $providerName
     */
    public function __construct(
        AuthenticationServiceInterface $authService,
        SecurityGroupServiceInterface $securityGroupService,
        string $providerName = self::PROVIDER_NAME
    ) {
        parent::__construct($providerName);
        $this->securityGroupService = $securityGroupService;
        $this->authService = $authService;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        $entity = $this->getEvent()->getParam('entity');

        if (!$entity instanceof Entity || !$entity->getEntity() instanceof GroupInterface) {
            return 'guest';
        }

        $group = $entity->getEntity();

        /**@var \Security\SecurityUser $authUser*/
        $authUser = $this->authService->getIdentity();

        if ($authUser->isSuper()) {
            return 'super';
        }

        return $this->securityGroupService->getRoleForGroup($group, $authUser);
    }
}
