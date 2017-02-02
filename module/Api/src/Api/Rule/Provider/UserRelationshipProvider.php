<?php

namespace Api\Rule\Provider;

use Rule\Event\Provider\AbstractEventProvider;
use Rule\Event\Provider\EventProviderInterface;
use Rule\Provider\ProviderInterface;
use Security\SecurityUser;
use Security\Service\SecurityUserServiceInterface;
use User\UserInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\Hal\Entity;

/**
 *  This provides relationship between active user and the user given
 */
class UserRelationshipProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = self::class;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var SecurityUserServiceInterface
     */
    protected $securityUserService;

    /**
     * UserRelationshipProvider constructor.
     * @param AuthenticationServiceInterface $authService
     * @param SecurityUserServiceInterface $securityUserService
     * @param string $providerName
     */
    public function __construct(
        AuthenticationServiceInterface $authService,
        SecurityUserServiceInterface $securityUserService,
        string $providerName = self::PROVIDER_NAME
    ) {
        parent::__construct($providerName);
        $this->authService = $authService;
        $this->securityUserService = $securityUserService;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $entity = $this->getEvent()->getParam('entity');

        if (!$entity instanceof Entity || !$entity->getEntity() instanceof UserInterface) {
            return 'guest';
        }

        $user = $entity->getEntity();

        /**@var SecurityUser $authUser*/
        $authUser = $this->authService->getIdentity();

        if ($authUser->isSuper()) {
            return 'super';
        }

        return $this->securityUserService->fetchRelationshipRole($authUser, $user);
    }
}
