<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class UserLink
 */
class UserLink extends Link
{
    /**
     * UserLink constructor.
     */
    public function __construct()
    {
        parent::__construct('user');
        $this->setProps(['label' => 'Friends and Network']);
        $this->setRoute('api.rest.user');
    }
}
