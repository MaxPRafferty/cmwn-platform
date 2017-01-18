<?php


namespace Org\Rule\Provider;

use Org\OrganizationInterface;
use Org\Service\OrganizationServiceInterface;
use Rule\Event\Provider\AbstractEventProvider;
use ZF\Hal\Entity;

/**
 * Class OrgGroupTypesProvider
 * @package Org\Rule\Provider
 */
class OrgGroupTypesProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = 'org-group-types';

    /**
     * @var OrganizationServiceInterface $orgService
     */
    protected $orgService;

    /**
     * OrgGroupTypesProvider constructor.
     * @param OrganizationServiceInterface $orgService
     * @param string $providerName
     */
    public function __construct(OrganizationServiceInterface $orgService, string $providerName = 'org-group-types')
    {
        parent::__construct($providerName);
        $this->orgService = $orgService;
    }

    /**
     * @return string[]
     */
    public function getValue()
    {
        $entity = $this->getEvent()->getParam('entity');

        if (!$entity instanceof Entity || !$entity->getEntity() instanceof OrganizationInterface) {
            return [];
        }

        return $this->orgService->fetchGroupTypes($entity->getEntity());
    }
}
