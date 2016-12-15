<?php

namespace Sa\Links;

use ZF\Hal\Link\Link;

/**
 * Class SuperAdminSettingsLink
 * @package Api\Links
 */
class SuperAdminSettingsLink extends Link
{
    /**
     * SuperAdminSettingsLink constructor.
     */
    public function __construct()
    {
        parent::__construct('sa_settings');
        $this->setProps(['label' => 'Super Admin Settings']);
        $this->setRoute('sa.rest.settings');
    }
}
