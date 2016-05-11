<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class UserNameLink
 */
class UserNameLink extends Link
{
    /**
     * UserNameLink constructor.
     */
    public function __construct()
    {
        parent::__construct('user_name');
        $this->setProps(['label' => 'Change User name']);
        $this->setRoute('api.rest.user-name');
    }
}
