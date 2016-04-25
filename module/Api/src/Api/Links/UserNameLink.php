<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class UserNameLink
 */
class UserNameLink extends Link
{
    public function __construct()
    {
        parent::__construct('user_name');
        $this->setRoute('api.rest.user-name');
    }
}
