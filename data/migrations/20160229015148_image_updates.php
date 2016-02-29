<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ImageUpdates extends AbstractMigration
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
        if (!$this->hasTable('images')) {
            $table = $this->table('images', ['id' => false, 'primary_key' => ['image_id']]);
            $table->addColumn('image_id', 'string')
                ->addColumn('url', 'string')
                ->addColumn('type', 'string', ['null' => true])
                ->addColumn('moderation_status', 'integer', ['limit' => MysqlAdapter::BLOB_TINY])
                ->addColumn('created', 'timestamp')
                ->addColumn('updated', 'timestamp')
                ->create();

            return;
        }

        $table = $this->table('images', ['id' => false, 'primary_key' => ['image_id']]);
        if (!$table->hasColumn('type')) {
            $table->addColumn('type', 'string', ['null' => true])
                ->update();
        }
    }
}
