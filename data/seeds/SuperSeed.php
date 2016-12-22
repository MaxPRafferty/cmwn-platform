<?php

use Phinx\Seed\AbstractSeed;

class SuperSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $table   = $this->table('users');
        $date    = new \DateTime();
        $table->insert([
            'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
            'username'   => 'admin',
            'email'      => 'admin@ginasink.com',
            'password'   => password_hash('business', PASSWORD_DEFAULT, ['cost' => 10]),
            'type'       => 'ADULT',
            'first_name' => 'Default',
            'last_name'  => 'Admin',
            'meta'       => '[]',
            'birthdate'  => null,
            'created'    => $date->format("Y-m-d H:i:s"),
            'updated'    => $date->format("Y-m-d H:i:s"),
            'super'      => 1,
        ])
            ->saveData();
    }
}
