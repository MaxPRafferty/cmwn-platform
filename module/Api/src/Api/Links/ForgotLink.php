<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class ForgotLink
 * @package Api\Links
 */
class ForgotLink extends Link
{
    public function __construct()
    {
        parent::__construct('forgot');
        $this->setRoute('api.rest.forgot');
    }
}
