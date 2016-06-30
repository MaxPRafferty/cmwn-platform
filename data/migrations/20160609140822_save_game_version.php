<?php

use Phinx\Migration\AbstractMigration;

class SaveGameVersion extends AbstractMigration
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
        $userSavesTable = $this->table(
            'user_saves',
            ['id' => false, 'primary_key' => ['user_id', 'game_id']]
        );

        if (!$userSavesTable->hasColumn('version')) {
            $userSavesTable->addColumn('version', 'string')
                ->update();
        }
    }
}
