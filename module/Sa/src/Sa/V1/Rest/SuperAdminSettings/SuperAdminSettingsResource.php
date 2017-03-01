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
     * @var array
     */
    protected $config;

    /**
     * SuperAdminSettingsResource constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param array $params
     * @return SuperAdminSettingsEntity
     */
    public function fetchAll($params = [])
    {
        return new SuperAdminSettingsEntity($this->config);
    }
}
