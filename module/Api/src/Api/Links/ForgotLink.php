<?php


namespace Api\Links;

use ZF\Hal\Link\Link;

class ForgotLink extends Link
{
    public function __construct()
    {
        parent::__construct('forgot');
        $this->setRoute('api.rest.forgot');
    }
}
