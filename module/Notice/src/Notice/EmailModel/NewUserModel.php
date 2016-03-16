<?php

namespace Notice\EmailModel;

use User\UserInterface;
use Zend\View\Model\ViewModel;

/**
 * Class NewUserModel
 */
class NewUserModel extends ViewModel
{
    /**
     * NewUserModel constructor.
     *
     * @param UserInterface $user
     * @param array $options
     */
    public function __construct(UserInterface $user, $options = [])
    {
        parent::__construct($user->getArrayCopy(), $options);
        $this->setTemplate('email/user/new.' . strtolower($user->getType()) . '.phtml');
    }
}
