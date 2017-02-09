<?php

use Phinx\Migration\AbstractMigration;

class GroupAddressMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $groupAddressTable = $this->table(
            'group_addresses',
            ['id' => false, 'primary_key' => ['group_id', 'address_id']]
        );

        $groupAddressTable->addColumn('group_id', 'string')
            ->addColumn('address_id', 'string')
            ->addForeignKey('group_id', 'groups', 'group_id', ['delete' => 'CASCADE'])
            ->addForeignKey('address_id', 'addresses', 'address_id', ['delete' => 'CASCADE'])
            ->create();
    }
}
