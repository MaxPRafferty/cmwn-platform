<?php

namespace Org\Rule\Provider;

use Org\Service\OrganizationServiceInterface;
use Rule\Provider\ProviderInterface;

/**
 * This provider gives the types of organizations in the system
 */
class OrgTypesProvider implements ProviderInterface
{
    const PROVIDER_NAME = 'org-types';

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
    public function __construct(OrganizationServiceInterface $orgService, string $providerName = self::PROVIDER_NAME)
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
