<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class SkribbleLink
 * @package Api\Links
 */
class SkribbleLink extends Link
{
    /**
     * SkribbleLink constructor.
     * @param string | UserInterface $user
     * @param null $skribbleId
     */
    public function __construct($user, $skribbleId = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('skribbles');
        $this->setProps(['label' => 'Skribbles']);
        $this->setRoute('api.rest.skribble', ['user_id' => $userId, 'skribble_id' => $skribbleId]);
    }
}
