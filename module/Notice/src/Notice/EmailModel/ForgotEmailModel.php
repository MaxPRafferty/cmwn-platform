<?php

namespace Notice\EmailModel;

use Zend\View\Model\ViewModel;

/**
 * Class ForgotEmailModel
 */
class ForgotEmailModel extends ViewModel
{
    /**
     * ForgotEmailModel constructor.
     * @param array $variables
     * @param array $options
     */
    public function __construct($variables, $options = [])
    {
        parent::__construct($variables, $options);
        $this->setTemplate('email/user/forgot.password.phtml');
    }
}
