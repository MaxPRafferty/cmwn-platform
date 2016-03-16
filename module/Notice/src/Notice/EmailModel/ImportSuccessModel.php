<?php

namespace Notice\EmailModel;

use Zend\View\Model\ViewModel;

/**
 * Class ImportSuccessModel
 *
 * ${CARET}
 */
class ImportSuccessModel extends ViewModel
{
    public function __construct(array $variables, $options = [])
    {
        parent::__construct($variables, $options);
        $this->setTemplate('email/import/success.phtml');
    }
}
