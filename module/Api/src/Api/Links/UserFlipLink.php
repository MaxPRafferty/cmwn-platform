<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class UserFlipLink
 */
class UserFlipLink extends Link
{
    /**
     * UserFlipLink constructor.
     *
     * @param null|string $userId
     */
    public function __construct($userId = null)
    {
        parent::__construct('user_flip');
        $this->setRoute('api.rest.flip-user', ['user_id' => $userId]);
    }
}
