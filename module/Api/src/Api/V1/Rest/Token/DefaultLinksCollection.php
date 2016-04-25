<?php

namespace Api\V1\Rest\Token;

use Api\Links\ForgotLink;
use Api\Links\LoginLink;
use Api\Links\LogoutLink;
use ZF\Hal\Link\LinkCollection;

/**
 * Class DefaultLinksCollection
 *
 * A Collection of default links when a Token Entity is created
 *
 * @deprecated
 */
class DefaultLinksCollection extends LinkCollection
{
    /**
     * DefaultLinksCollection constructor.
     */
    public function __construct()
    {
        $this->add(new LoginLink());
        $this->add(new LogoutLink());
        $this->add(new ForgotLink());
    }
}
