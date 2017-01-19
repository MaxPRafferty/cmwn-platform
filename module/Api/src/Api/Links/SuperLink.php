<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class SuperLink
 * @package Api\Links
 */
class SuperLink extends Link
{
    /**
     * SuperLink constructor.
     * @param null $userId
     */
    public function __construct($userId = null)
    {
        parent::__construct('super');
        $this->setProps(['label' => 'Super']);
        $this->setRoute('api.rest.super', ['user_id' => $userId]);
    }
}
