<?php

use Phinx\Migration\AbstractMigration;
use \Phinx\Db\Adapter\MysqlAdapter;

class SuperUserFlag extends AbstractMigration
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
        $table = $this->table('users', ['id' => false, 'primary_key' => ['user_id']]);

        if (!$this->hasTable('users')) {
            $table->addColumn('user_id', 'string')
                ->addColumn('username', 'string', ['limit' => 60])
                ->addColumn('email', 'string')
                ->addColumn('code', 'string', ['null' => true])
                ->addColumn('type', 'enum', ['values' => ['CHILD', 'ADULT']])
                ->addColumn('password', 'string', ['null' => true])
                ->addColumn('first_name', 'string')
                ->addColumn('middle_name', 'string', ['null' => true])
                ->addColumn('last_name', 'string')
                ->addColumn('gender', 'string', ['null' => true])
                ->addColumn('meta', 'text', ['null' => true])
                ->addColumn('birthdate', 'timestamp')
                ->addColumn('super', 'integer', ['limit' => MysqlAdapter::BLOB_TINY, 'null' => true, 'default' => 0])
                ->addColumn('created', 'timestamp')
                ->addColumn('updated', 'timestamp')
                ->addColumn('deleted', 'timestamp', ['null' => true])
                ->addColumn('code_created', 'timestamp')
                ->addIndex(['email'], ['unique' => true])
                ->create();

            return;
        }

        if (!$table->hasColumn('super')) {
            $table->addColumn('super', 'integer', ['limit' => MysqlAdapter::BLOB_TINY, 'null' => true, 'default' => 0])
                ->save();
        }
    }
}
