<?php

namespace Notice\EmailModel;

use Zend\View\Model\ViewModel;

/**
 * Class NewUserModel
 */
class NewUserModel extends ViewModel
{
    /**
     * NewUserModel constructor.
     *
     * @param array $variables
     * @param array $options
     */
    public function __construct($variables, $options = [])
    {
        parent::__construct($variables, $options);
    }
}
