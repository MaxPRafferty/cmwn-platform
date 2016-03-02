<?php


namespace Api\Links;

use ZF\Hal\Link\Link;

class LoginLink extends Link
{
    public function __construct()
    {
        parent::__construct('login');
        $this->setRoute('api.rest.login');
    }
}
