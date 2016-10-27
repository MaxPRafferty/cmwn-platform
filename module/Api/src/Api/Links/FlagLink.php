<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class FlagLink
 * @package Api\Links
 */
class FlagLink extends Link
{
    /**
     * FlagLink constructor.
     */
    public function __construct()
    {
        parent::__construct('flags');
        $this->setProps(['label'=>'Flags']);
        $this->setRoute('api.rest.flag');
    }
}
