<?php

use Phinx\Migration\AbstractMigration;

class AddressMigration extends AbstractMigration
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
        $addressTable = $this->table(
            'addresses',
            ['id' => false, 'primary_key' =>['address_id']]
        );

        $addressTable->addColumn('address_id', 'string')
            ->addColumn('administrative_area', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('sub_administrative_area', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('locality', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('dependent_locality', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('postal_code', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('thoroughfare', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('premise', 'string', ['limit' => 255, 'null' => false])
            ->create();
    }
}
