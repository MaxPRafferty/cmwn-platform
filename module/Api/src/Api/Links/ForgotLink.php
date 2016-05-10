<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class ForgotLink
 * @package Api\Links
 */
class ForgotLink extends Link
{
    /**
     * ForgotLink constructor.
     */
    public function __construct()
    {
        parent::__construct('forgot');
        $this->setProps(['label' => 'Forgot Password']);
        $this->setRoute('api.rest.forgot');
    }
}
