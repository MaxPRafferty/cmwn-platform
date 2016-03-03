<?php

namespace Api\V1\Rest\Logout;

use Api\Links\LoginLink;
use ZF\Hal\Entity;

/**
 * Class LogoutEntity
 * @package Api\V1\Rest\Logout
 */
class LogoutEntity extends Entity
{
    public function __construct()
    {
        parent::__construct(['logout' => true]);
        $this->getLinks()->add(new LoginLink());
    }
}
