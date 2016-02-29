<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class UserMigration extends AbstractMigration
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
     * Remember to call "create()" or "update()" and NOT "create()" when working
     * with the Table class.
     */
    public function up()
    {
        $this->upUsers()
            ->upOrganizations()
            ->upGroups()
            ->upImages()
            ->upGames()
            ->upPivots();
    }

    protected function upUsers()
    {
        if ($this->hasTable('users')) {
            return $this;
        }

        $table = $this->table('users', ['id' => false, 'primary_key' => ['user_id']]);
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
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->addColumn('deleted', 'timestamp', ['null' => true])
            ->addColumn('code_created', 'timestamp')
            ->addIndex(['email'], ['unique' => true])
            ->create();

        return $this;
    }

    protected function upOrganizations()
    {
        if ($this->hasTable('organizations')) {
            return $this;
        }

        $table = $this->table('organizations', ['id' => false, 'primary_key' => ['org_id']]);
        $table->addColumn('org_id', 'string')
            ->addColumn('title', 'string')
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('meta', 'text', ['null' => true])
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->addColumn('deleted', 'timestamp', ['null' => true])
            ->create();

        return $this;
    }

    protected function upGroups()
    {
        if ($this->hasTable('groups')) {
            return $this;
        }

        $table = $this->table('groups', ['id' => false, 'primary_key' => ['group_id']]);
        $table->addColumn('group_id', 'string')
            ->addColumn('organization_id', 'string')
            ->addColumn('title', 'string')
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('meta', 'text', ['null' => true])
            ->addColumn('left', 'integer', ['signed' => false])
            ->addColumn('right', 'integer', ['signed' => false])
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->addColumn('deleted', 'timestamp', ['null' => true])
            ->create();

        $table->addForeignKey(
            'organization_id',
            'organizations',
            'org_id',
            ['delete' => 'CASCADE', 'update'=> 'NO_ACTION']
        )
            ->update();

        return $this;
    }

    protected function upImages()
    {
        if ($this->hasTable('images')) {
            return $this;
        }

        $table = $this->table('images', ['id' => false, 'primary_key' => ['image_id']]);
        $table->addColumn('image_id', 'string')
            ->addColumn('url', 'string')
            ->addColumn('moderation_status', 'integer', ['limit' => MysqlAdapter::BLOB_TINY])
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->create();

        return $this;
    }

    protected function upGames()
    {
        if ($this->hasTable('games')) {
            return $this;
        }

        $table = $this->table('games', ['id' => false, 'primary_key' => ['game_id']]);
        $table->addColumn('game_id', 'string')
            ->addColumn('url', 'string')
            ->addColumn('moderation_status', 'integer', ['limit' => MysqlAdapter::BLOB_TINY])
            ->addColumn('created', 'timestamp')
            ->addColumn('updated', 'timestamp')
            ->create();

        return $this;
    }

    protected function upPivots()
    {
        if (!$this->hasTable('user_groups')) {
            $table = $this->table('user_groups', ['id' => false, 'primary_key' => ['user_id', 'group_id']]);
            $table->addColumn('user_id', 'string')
                ->addColumn('group_id', 'string')
                ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE'])
                ->addForeignKey('group_id', 'groups', 'group_id', ['delete' => 'CASCADE'])
                ->create();
        }

        if ($this->hasTable('images')) {
            $table = $this->table('user_images', ['id' => false, 'primary_key' => ['user_id', 'image_id']]);
            $table->addColumn('user_id', 'string')
                ->addColumn('image_id', 'string')
                ->addForeignKey('user_id', 'users', 'user_id', ['delete' => 'CASCADE'])
                ->addForeignKey('image_id', 'images', 'image_id', ['delete' => 'CASCADE'])
                ->create();
        }

        return $this;
    }
}
