<?php


namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class PasswordLink
 *
 * @package Api\Links
 */
class PasswordLink extends Link
{
    public function __construct($userId)
    {
        $userId = $userId instanceof UserInterface ? $userId->getUserId() : $userId;
        parent::__construct('password');
        $this->setRoute('api.rest.password', ['user_id' => $userId]);
    }
}
