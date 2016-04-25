<?php

namespace Notice\EmailModel;

use Zend\View\Model\ViewModel;

/**
 * Class ImportSuccessModel
 *
 * Email View Model for when the Import is successful
 */
class ImportSuccessModel extends ViewModel
{
    /**
     * ImportSuccessModel constructor.
     * @param array $variables
     * @param array $options
     */
    public function __construct(array $variables, $options = [])
    {
        parent::__construct($variables, $options);
        $this->setTemplate('email/import/success.phtml');
    }
}
