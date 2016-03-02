<?php


namespace Api\Links;


use ZF\Hal\Link\Link;

class LogoutLink extends Link
{
    public function __construct()
    {
        parent::__construct('logout');
        $this->setRoute('api.rest.logout');
    }
}
