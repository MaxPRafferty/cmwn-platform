<?php

namespace Org\Rule\Provider;

use Org\Service\OrganizationServiceInterface;
use Rule\Provider\ProviderInterface;

/**
 * Class OrgTypeProvider
 * This provider gives the types of organizations in the system
 */
class OrgTypesProvider implements ProviderInterface
{
    /**
     * @var OrganizationServiceInterface $orgService
     */
    protected $orgService;

    /**
     * @var string
     */
    protected $providerName = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->providerName;
    }

    /**
     * OrgTypeProvider constructor.
     * @param OrganizationServiceInterface $orgService
     * @param string $providerName
     */
    public function __construct(OrganizationServiceInterface $orgService, string $providerName = 'org-types')
    {
        $this->orgService = $orgService;
        $this->providerName = $providerName;
    }

    /**
     * @return string[]
     */
    public function getValue()
    {
        return $this->orgService->fetchOrgTypes();
    }
}
