<?php

use Phinx\Migration\AbstractMigration;

class UserName extends AbstractMigration
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
        if ($this->hasTable('names')) {
            return;
        }

        $table = $this->table('names', ['id' => false, 'primary_key' => ['name']]);
        $table->addColumn('name', 'string')
            ->addColumn('position', 'enum', ['values' => ['LEFT', 'RIGHT']])
            ->addColumn('count', 'integer', ['signed' => false])
            ->addIndex(['name', 'position'], ['unique' => true])
            ->create();
    }
}
