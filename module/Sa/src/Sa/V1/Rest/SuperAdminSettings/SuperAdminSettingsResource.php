<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use ZF\Rest\AbstractResourceListener;

/**
 * Class SuperAdminResource
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsResource extends AbstractResourceListener
{
    /**
     * @param array $params
     * @return SuperAdminSettingsEntity
     */
    public function fetchAll($params = [])
    {
        return new SuperAdminSettingsEntity([]);
    }
}
