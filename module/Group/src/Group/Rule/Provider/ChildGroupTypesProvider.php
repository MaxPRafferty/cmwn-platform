<?php

namespace Group\Rule\Provider;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Rule\Event\Provider\AbstractEventProvider;
use ZF\Hal\Entity;

/**
 * Class ChildGroupTypesProvider
 * this provider gives the types of child groups a group has
 */
class ChildGroupTypesProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = 'child-group-types';
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * ChildGroupTypesProvider constructor.
     * @param GroupServiceInterface $groupService
     * @param string $providerName
     */
    public function __construct(GroupServiceInterface $groupService, string $providerName = self::PROVIDER_NAME)
    {
        parent::__construct($providerName);
        $this->groupService = $groupService;
    }

    /**
     * @return array | string[]
     */
    public function getValue()
    {
        $entity = $this->getEvent()->getParam('entity');

        if (!$entity instanceof Entity || !$entity->getEntity() instanceof GroupInterface) {
            return [];
        }

        return $this->groupService->fetchChildTypes($entity->getEntity());
    }
}
