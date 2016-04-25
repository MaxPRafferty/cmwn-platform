<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class LoginLink
 *
 * Hal Link for Login
 */
class LoginLink extends Link
{
    /**
     * LoginLink constructor.
     */
    public function __construct()
    {
        parent::__construct('login');
        $this->setRoute('api.rest.login');
    }
}
