<?php

namespace Api\Controller;

use Api\SwaggerHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * A Controller that will dynamically generate swagger.json spec
 */
class SwaggerController extends AbstractActionController
{
    /**
     * @var SwaggerHelper
     */
    protected $swaggerHelper;

    /**
     * SwaggerController constructor.
     *
     * @param SwaggerHelper $helper
     */
    public function __construct(SwaggerHelper $helper)
    {
        $this->swaggerHelper = $helper;
    }

    /**
     * Scans the directory and makes the swagger docs
     *
     * @return JsonModel
     */
    public function swaggerAction()
    {
        return new JsonModel($this->swaggerHelper->getSwagger());
    }
}
