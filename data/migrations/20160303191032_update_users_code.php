<?php

use Phinx\Migration\AbstractMigration;

class UpdateUsersCode extends AbstractMigration
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
        if ($table->hasColumn('code_created')) {
            $table->removeColumn('code_created')
                ->update();
        }

        if (!$table->hasColumn('code_expires')) {
            $table->addColumn('code_expires', 'timestamp')
                ->update();
        }

        return null;
    }
}
