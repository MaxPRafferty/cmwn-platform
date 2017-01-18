<?php

namespace Api\Rule\Provider;

use Api\Rule\Provider\EntityFromEventProvider;
use Group\GroupInterface;
use Rule\Event\Provider\AbstractEventProvider;
use Rule\Event\Provider\EventProviderInterface;
use Security\Service\SecurityGroupServiceInterface;
use Security\Service\SecurityOrgServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\Hal\Entity;

/**
 * Class GroupEntityProvider
 * @package Api\Rule\Rule
 */
class ActiveUserGroupRoleProvider extends AbstractEventProvider
{
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
        string $providerName = ActiveUserGroupRoleProvider::class
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
