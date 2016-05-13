<?php

use Phinx\Migration\AbstractMigration;

class Flips extends AbstractMigration
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
        $flipTable = $this->table('flips', ['id' => false, 'primary_key' => 'flip_id']);

        $flipTable->addColumn('flip_id', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->create();


        $userFlipsTable = $this->table(
            'user_flips',
            ['id' => false, 'primary_key' => ['user_id', 'flip_id', 'earned']]
        );

        $userFlipsTable->addColumn('flip_id', 'string')
            ->addColumn('user_id', 'string')
            ->addColumn('earned', 'timestamp')
            ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE'])
            ->addForeignKey('flip_id', 'flips', 'flip_id', ['delete' => 'CASCADE'])
            ->create();
    }
}
