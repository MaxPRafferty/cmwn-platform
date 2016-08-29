<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class SkribbleLink
 * @package Api\Links
 */
class SkribbleLink extends Link
{
    /**
     * SkribbleLink constructor.
     * @param string $userId
     * @param null $skribbleId
     */
    public function __construct($userId, $skribbleId = null)
    {
        parent::__construct('skribbles');
        $this->setProps(['label' => 'Skribbles']);
        $this->setRoute('api.rest.skribble', ['user_id' => $userId, 'skribble_id' => $skribbleId]);
    }
}
