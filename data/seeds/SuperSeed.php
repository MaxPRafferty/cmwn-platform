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
        $table = $this->table('users');

        $date = new \DateTime();
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'mfafferty',
                'email'      => 'max@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => '$2y$10$J9fRJQkrFfkfN7edCGvZb.MIZrukyffFmypLYiwRba.HdlOV6JUxm',
                'first_name' => 'Max',
                'last_name'  => 'Rafferty',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->getTimestamp(),
                'updated'    => $date->getTimestamp(),
                'super'      => 1
            ])
                ->save();
        
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'jalbers',
                'email'      => 'joni@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => '$2y$10$J9fRJQkrFfkfN7edCGvZb.MIZrukyffFmypLYiwRba.HdlOV6JUxm',
                'first_name' => 'Joni',
                'last_name'  => 'Albers',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->getTimestamp(),
                'updated'    => $date->getTimestamp(),
                'super'      => 1
            ])
                ->save();
        
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'manchuck',
                'email'      => 'chuck@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => '$2y$10$pN.agLiJnp4DgSrjBv8Sy.olHnPj42dFGfd/FBZFjt7mKUqyhe2JS',
                'first_name' => 'Chuck',
                'last_name'  => 'Reeves',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->getTimestamp(),
                'updated'    => $date->getTimestamp(),
                'super'      => 1
            ])
                ->save();


    }
}
