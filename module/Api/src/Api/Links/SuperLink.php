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
     */
    public function __construct()
    {
        parent::__construct('super');
        $this->setProps(['label' => 'Super']);
        $this->setRoute('api.rest.super', [], ['reuse_matched_params' => false]);
    }
}
