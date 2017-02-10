<?php

namespace Api\Controller;

use Api\SwaggerHelper;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;

/**
 * A Controller that will dynamically generate swagger.json spec
 */
class SwaggerController extends AbstractController
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
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $event)
    {
        $request = $event->getRequest();
        $host    = null;
        if ($request instanceof Request) {
            $host = $request->getUri()->getHost();
        }

        $event->setResult(new JsonModel($this->swaggerHelper->getSwagger($host)));

        return $event->getResult();
    }
}
