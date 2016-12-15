<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\UserLink;
use ZF\Hal\Entity;

/**
 * Class SuperAdminSettingsEntity
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsEntity extends Entity
{
    /**
     * SuperAdminSettingsEntity Constructor
     */
    public function __construct()
    {
        parent::__construct([]);
        $userLink = new UserLink();
        $userLink->setProps(['label' => 'Manage Users']);
        $this->getLinks()->add($userLink);
    }
}
