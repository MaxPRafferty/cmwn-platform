<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class FeedLink
 * @package Api\Links
 */
class FeedLink extends Link
{
    /**
     * FeedLink constructor.
     */
    public function __construct()
    {
        parent::__construct('feed');
        $this->setProps(['label' => 'Feed']);
        $this->setRoute('api.rest.feed', [], ['reuse_matched_params' => false]);
    }
}
