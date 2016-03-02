<?php

namespace Api\V1\Rest\Token;

use Api\Links\ForgotLink;
use Api\Links\LoginLink;
use Api\Links\LogoutLink;
use ZF\Hal\Link\LinkCollection;

class DefaultLinksCollection extends LinkCollection
{
    public function __construct()
    {
        $this->add(new LoginLink());
        $this->add(new LogoutLink());
        $this->add(new ForgotLink());
    }
}
