<?php

namespace Security\Controller;

use Application\Utils\Text\Table\Decorator\Csv;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Utils\PermissionTableFactory;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Text\Table\Decorator\Ascii;

/**
 * Class PermController
 */
class PermController extends AbstractConsoleController implements RbacAwareInterface
{
    use RbacAwareTrait;

    /**
     * @var PermissionTableFactory
     */
    protected $permissionBuilder;

    /**
     * PermController constructor.
     *
     * @param PermissionTableFactory $rbac
     */
    public function __construct(PermissionTableFactory $rbac)
    {
        $this->permissionBuilder = $rbac;
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function showPermAction()
    {
        $roleString = $this->getEvent()->getRouteMatch()->getParam('roles', false);
        $csvString  = $this->getEvent()->getRouteMatch()->getParam('csv', false);
        $roles      = $roleString !== false ? explode(',', $roleString) : [];

        $table = $this->permissionBuilder->build($roles);

        if ($csvString === false) {
            $table->setDecorator(new Ascii());
            $this->getResponse()->setContent($table->__toString());
            return $this->getResponse();
        }

        $table->setDecorator(new Csv());
        file_put_contents($csvString, $table->__toString());
        return $this->getResponse();
    }
}
