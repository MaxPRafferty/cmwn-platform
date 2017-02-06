<?php

namespace Group\Rule\Provider;

use Group\Service\GroupServiceInterface;
use Rule\Provider\ProviderInterface;

/**
 * this provider returns all the group types in the system
 */
class GroupTypesProvider implements ProviderInterface
{
    const PROVIDER_NAME = 'group-types';

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var string
     */
    protected $providerName = '';

    /**
     * GroupTypeProvider constructor.
     * @param GroupServiceInterface $groupService
     * @param string $providerName
     */
    public function __construct(GroupServiceInterface $groupService, string $providerName = self::PROVIDER_NAME)
    {
        $this->groupService = $groupService;
        $this->providerName = $providerName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->providerName;
    }

    /**
     * @return string[]
     */
    public function getValue()
    {
        return $this->groupService->fetchGroupTypes();
    }
}
