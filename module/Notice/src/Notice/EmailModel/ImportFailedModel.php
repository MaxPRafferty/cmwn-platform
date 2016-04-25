<?php

namespace Notice\EmailModel;

use ZF\ContentNegotiation\ViewModel;

/**
 * Class ImportFailedModel
 */
class ImportFailedModel extends ViewModel
{
    /**
     * ImportFailedModel constructor.
     * @param array $variables
     * @param array $options
     */
    public function __construct(array $variables, $options = [])
    {
        if (!isset($variables['errors'])) {
            throw new \RuntimeException('Missing required variables for view');
        }

        parent::__construct($variables, $options);
        $this->setTemplate('email/import/failed.phtml');
    }
}
