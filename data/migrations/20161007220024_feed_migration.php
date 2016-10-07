<?php

use Phinx\Migration\AbstractMigration;

class FeedMigration extends AbstractMigration
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
        $feedTable = $this->table(
            'feed',
            ['id' => false, 'primary_key' => ['feed_id']]
        );

        $feedTable->addColumn('feed_id', 'string')
            ->addColumn('sender', 'string', ['null' => true])
            ->addColumn('title', 'string')
            ->addColumn('message', 'string', ['null' => true])
            ->addColumn('priority', 'integer', ['signed' => false])
            ->addColumn('created', 'timestamp', ['default' => false])
            ->addColumn('updated', 'timestamp', ['default'=>'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted', 'timestamp', ['null' => true])
            ->addColumn('posted', 'timestamp', ['null' => true])
            ->addColumn('type', 'string')
            ->addColumn('visibility', 'integer', ['signed' => true])
            ->addColumn('meta', 'text', ['null' => true])
            ->addColumn('type_version', 'string')
            ->create();
    }
}
