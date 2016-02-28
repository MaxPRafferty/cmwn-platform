<?php

namespace Api\V1\Rest\Org;

use Org\Service\OrganizationServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserResourceFactory
 * @package Api\V1\Rest\User
 */
class OrgResourceFactory
{
    /**
     * @param ServiceLocatorInterface $services
     * @return OrgResource
     */
    public function __invoke(ServiceLocatorInterface $services)
    {
        /** @var OrganizationServiceInterface $orgService */
        $orgService = $services->get('Org\Service');
        return new OrgResource($orgService);
    }
}
