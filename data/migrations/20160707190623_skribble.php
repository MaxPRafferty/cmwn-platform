<?php
// @@codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Skribble extends AbstractMigration
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
        if ($this->hasTable('skribbles')) {
            return $this;
        }

        $table = $this->table('skribbles', ['id' => false, 'primary_key' => ['skribble_id']]);
        $table->addColumn('skribble_id', 'string')
            ->addColumn('version', 'string')
            ->addColumn('url', 'string', ['null' => true])
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->addColumn('deleted', 'timestamp', ['null' => true])
            ->addColumn('status', 'enum', ['values' => ['COMPLETE', 'PROCESSING', 'NOT_COMPLETE', 'ERROR']])
            ->addColumn('created_by', 'string')
            ->addColumn('friend_to', 'string')
            ->addColumn('read', 'integer', ['limit' => MysqlAdapter::BLOB_TINY])
            ->addForeignKey('created_by', 'users', 'user_id', ['delete' => 'CASCADE'])
            ->addForeignKey('friend_to', 'users', 'user_id', ['delete' => 'CASCADE'])
            ->create();
    }
}
