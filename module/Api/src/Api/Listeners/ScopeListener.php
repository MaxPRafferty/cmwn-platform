<?php

namespace Api\Listeners;

use Api\ScopeAwareInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Security\Service\SecurityUserServiceInterface;
use User\UserInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class ScopeListener
 */
class ScopeListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use RbacAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SecurityUserServiceInterface
     */
    protected $securityGroupService;

    /**
     * ScopeListener constructor.
     * @param SecurityUserServiceInterface $securityGroupService
     */
    public function __construct(SecurityUserServiceInterface $securityGroupService)
    {
        $this->securityGroupService = $securityGroupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach($listener, 'ZF\Hal\Plugin\Hal');
        }
    }

    /**
     * @param EventInterface $event
     */
    public function onRender(EventInterface $event)
    {
        // Should never be able to load a scope object
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        $entity  = $event->getParam('entity');
        $payload = $event->getParam('payload');
        if (!$entity instanceof Entity) {
            return;
        }

        if (!$entity->getEntity() instanceof ScopeAwareInterface) {
            return;
        }

        $role    = $this->getRole($entity);
        $payload['scope'] =$this->rbac->getScopeForEntity($role, $entity->getEntity()->getEntityType());
    }

    /**
     * Gets the role that the user currently has
     *
     * @param Entity $entity
     * @return string
     */
    protected function getRole(Entity $entity)
    {
        try {
            $user = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        if ($user instanceof SecurityUser && $user->isSuper()) {
            return $user->getRole();
        }

        $realEntity = $entity->getEntity();
        if ($realEntity instanceof UserInterface) {
            return $this->securityGroupService->fetchRelationshipRole($user, $realEntity);
        }

        if ($user instanceof SecurityUser) {
            return $user->getRole();
        }

        return 'logged_in';
    }
}
