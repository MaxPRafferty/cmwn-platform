<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\GameDataLink;
use Api\Links\GameLink;
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

        $gameLink = new GameLink();
        $gameLink->setProps(['label' => 'Manage Games']);

        $gameDataLink = new GameDataLink('all-about-you');
        $gameDataLink->setProps(['label' => 'Survey Results']);

        $this->getLinks()->add($userLink);
        $this->getLinks()->add($gameLink);
        $this->getLinks()->add($gameDataLink);
    }
}