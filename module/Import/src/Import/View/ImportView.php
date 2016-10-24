<?php

namespace Import\View;

use Zend\View\Model\ViewModel;

/**
 * Class ImportView
 */
class ImportView extends ViewModel
{
    /**
     * ImportView constructor.
     *
     * @param array|null|\Traversable $variables
     * @param bool $error
     * @param array $options
     */
    public function __construct($variables, $error = false, $options = [])
    {
        parent::__construct($variables, $options);

        $this->setTerminal(true);
        $this->setTemplate('import/import.phtml');

        if ($error) {
            $this->setTemplate('import/import.error.phtml');
        }
    }
}
