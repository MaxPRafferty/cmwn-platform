<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class GameUpdateMigration extends AbstractMigration
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
        $table = $this->table('games');
        if (!$table->hasColumn('global')) {
            $table->addColumn('global', 'integer', ['limit' => MysqlAdapter::BLOB_TINY, 'null' => true, 'default' => 0])
                ->save();
        }

        $this->execute(
            'UPDATE games ' .
            'SET global = 1'
        );
    }
}
