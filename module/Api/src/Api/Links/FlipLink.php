<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class FlipLink
 */
class FlipLink extends Link
{
    /**
     * FlipLink constructor.
     */
    public function __construct()
    {
        parent::__construct('flip');
        $this->setProps(['label' => 'Discover Flips']);
        $this->setRoute('api.rest.flip', [], ['reuse_matched_params' => false]);
    }
}
