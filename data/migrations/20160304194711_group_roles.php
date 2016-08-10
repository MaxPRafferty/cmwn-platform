<?php
// @@codingStandardsIgnoreStart
use Phinx\Migration\AbstractMigration;

/**
 * Class GroupRoles
 */
class GroupRoles extends AbstractMigration
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
        $table = $this->table('user_groups', ['id' => false, 'primary_key' => ['user_id', 'group_id']]);

        if (!$this->hasTable('user_groups')) {
            $table = $this->table('user_groups', ['id' => false, 'primary_key' => ['user_id', 'group_id']]);
            $table->addColumn('user_id', 'string')
                ->addColumn('group_id', 'string')
                ->addColumn('role', 'string')
                ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE'])
                ->addForeignKey('group_id', 'groups', 'group_id', ['delete' => 'CASCADE'])
                ->create();

            return;
        }

        if (!$table->hasColumn('role')) {
            $table->addColumn('role', 'string')
                ->save();
        }
    }
}
