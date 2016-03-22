<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddCommingSoon extends AbstractMigration
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
        if (!$this->hasTable('games')) {
            return $this;
        }

        $table = $this->table('games', ['id' => false, 'primary_key' => ['game_id']]);
        if (!$table->hasColumn('coming_soon')) {
            $table->addColumn('coming_soon', 'integer', ['limit' => MysqlAdapter::BLOB_TINY, 'default' => 1])
            ->update();
        }
    }
}
