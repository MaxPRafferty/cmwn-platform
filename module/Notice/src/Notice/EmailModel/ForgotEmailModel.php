<?php

namespace Notice\EmailModel;

use User\UserInterface;
use Zend\View\Model\ViewModel;

/**
 * Class ForgotEmailModel
 */
class ForgotEmailModel extends ViewModel
{
    /**
     * ForgotEmailModel constructor.
     *
     * @param UserInterface $user
     * @param string $code
     * @param array $options
     */
    public function __construct(UserInterface $user, $code, $options = [])
    {
        $vars = [
            'user' => $user->getArrayCopy(),
            'code' => $code
        ];

        parent::__construct($vars, $options);
        $this->setTemplate('email/user/forgot.password.phtml');
    }
}
