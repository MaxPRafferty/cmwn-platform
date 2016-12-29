<?php

namespace Security\Utils;

use Security\Authorization\Rbac;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Zend\Text\Table\Column;
use Zend\Text\Table\Row;
use Zend\Text\Table\Table;

/**
 * Class PermissionTableBuilder
 * @todo move to dev module
 */
class PermissionTableFactory implements RbacAwareInterface
{
    use RbacAwareTrait;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $roles = [];

    /**
     * @var array
     */
    protected $permissions = [];

    /**
     * PermissionTableBuilder constructor.
     *
     * @param Rbac $rbac
     */
    public function __construct(Rbac $rbac)
    {
        $this->setRbac($rbac);
        foreach ($rbac as $role) {
            array_push($this->roles, $role->getName());
        }

        $this->permissions = $rbac->getPermissions();
    }

    /**
     * Creates the Table
     */
    public function build($roles = [])
    {
        $header = $this->buildHeaderRow($roles);
        $width  = array_fill(0, count($header->getColumns()) + 1, 20);
        $width[0] = 44;
        $this->table = new Table([
            'columnwidths' => $width,
        ]);

        $this->table->appendRow($header);

        $this->buildRowsForPermissions($roles);
        return $this->table;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return Row
     */
    protected function buildHeaderRow($roles = [])
    {
        $header = new Row();
        $header->appendColumn(new Column('Permission Label'));
        $header->appendColumn(new Column('Permission'));

        $roles = !empty($roles) ? $roles : $this->roles;

        array_walk($roles, function ($role) use (&$header) {
            $header->appendColumn(new Column($role));
        });

        return $header;
    }

    /**
     * @param array $roles
     */
    protected function buildRowsForPermissions($roles = [])
    {
        $roles = !empty($roles) ? $roles : $this->roles;
        foreach ($this->permissions as $permission => $label) {
            $this->buildPermissionRow($permission, $label, $roles);
        }
    }

    /**
     * @param $permission
     * @param $label
     * @param array $roles
     */
    protected function buildPermissionRow($permission, $label, array $roles)
    {
        $permissionRow = new Row();
        $permissionRow->appendColumn(new Column($label));
        $permissionRow->appendColumn(new Column($permission));
        array_walk($roles, function ($role) use ($permissionRow, $permission) {
            $permissionRow->appendColumn(
                new Column(
                    $this->rbac->isGranted($role, $permission) ? 'Yes' : 'No'
                )
            );
        });

        $this->table->appendRow($permissionRow);
    }
}
