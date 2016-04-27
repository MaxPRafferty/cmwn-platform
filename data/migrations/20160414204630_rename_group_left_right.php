<?php

use Phinx\Migration\AbstractMigration;

class RenameGroupLeftRight extends AbstractMigration
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
        $table = $this->table('groups', ['id' => false, 'primary_key' => ['group_id']]);

        if ($table->hasColumn('lft')) {
            $table->renameColumn('lft', 'head');
        }

        if ($table->hasColumn('rgt')) {
            $table->renameColumn('rgt', 'tail');
        }

        if (!$table->hasColumn('depth')) {
            $table->addColumn('depth', 'integer', ['signed' => false])->update();
        }
    }
}
