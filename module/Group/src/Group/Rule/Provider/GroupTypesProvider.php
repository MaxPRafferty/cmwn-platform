<?php

namespace Group\Rule\Provider;

use Group\Service\GroupServiceInterface;
use Rule\Provider\ProviderInterface;

/**
 * Class GroupTypeProvider
 * this provider returns all the group types in the system
 */
class GroupTypesProvider implements ProviderInterface
{
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
    public function __construct(GroupServiceInterface $groupService, string $providerName = 'group-types')
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
