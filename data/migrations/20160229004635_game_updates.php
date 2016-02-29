<?php

use Phinx\Migration\AbstractMigration;

class GameUpdates extends AbstractMigration
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
            $table = $this->table('games', ['id' => false, 'primary_key' => ['game_id']]);
            $table->addColumn('game_id', 'string')
                ->addColumn('title', 'string', ['null' => true])
                ->addColumn('description', 'text', ['null' => true])
                ->addColumn('created', 'timestamp')
                ->addColumn('updated', 'timestamp')
                ->addColumn('deleted', 'timestamp', ['null' => true])
                ->create();
        }

        $table = $this->table('games', ['id' => false, 'primary_key' => ['game_id']]);

        if ($table->hasColumn('url')) {
            $table->removeColumn('url');
        }

        if ($table->hasColumn('moderation_status')) {
            $table->removeColumn('moderation_status');
        }

        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', ['null' => true]);
        }

        if (!$table->hasColumn('description')) {
            $table->addColumn('description', 'text', ['null' => true]);
        }

        if (!$table->hasColumn('deleted')) {
            $table->addColumn('deleted', 'timestamp', ['null' => true]);
        }

        $table->update();
        return null;
    }
}
