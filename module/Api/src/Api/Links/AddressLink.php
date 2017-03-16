<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * hal link for address service
 */
class AddressLink extends Link
{
    /**
     * AddressLink constructor.
     */
    public function __construct()
    {
        parent::__construct('address');
        $this->setProps(['label' => 'Address']);
        $this->setRoute('api.rest.address', [], ['reuse_matched_params' => false]);
    }
}
