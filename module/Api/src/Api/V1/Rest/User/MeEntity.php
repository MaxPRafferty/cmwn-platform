<?php

namespace Api\V1\Rest\User;

use Api\Links\ForgotLink;
use Api\Links\GameLink;
use Api\Links\MeLink;
use Api\Links\ProfileLink;
use User\UserInterface;
use ZF\Hal\Entity;

class MeEntity extends Entity
{
    public function __construct(UserInterface $user)
    {
        parent::__construct($user->getArrayCopy());
        $this->getLinks()->add(new MeLink($user));
        $this->getLinks()->add(new ProfileLink($user));
        $this->getLinks()->add(new ForgotLink());
        $this->getLinks()->add(new GameLink());
    }
}
