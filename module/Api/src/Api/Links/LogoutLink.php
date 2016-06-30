<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class LogoutLink
 *
 * Hal Link for Logout
 */
class LogoutLink extends Link
{
    /**
     * LogoutLink constructor.
     */
    public function __construct()
    {
        parent::__construct('logout');
        $this->setProps(['label' => 'Logout']);
        $this->setRoute('api.rest.logout');
    }
}
