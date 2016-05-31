<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class ResetLink
 */
class ResetLink extends Link
{
    /**
     * ResetLink constructor.
     *
     * @param string $userId
     */
    public function __construct($userId)
    {
        parent::__construct('reset');
        $this->setProps(['label' => 'Reset Password']);
        $this->setRoute('api.rest.reset', ['user_id' => $userId], ['reuse_matched_params' => false]);
    }
}
