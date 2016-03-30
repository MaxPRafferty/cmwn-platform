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
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'mfafferty',
                'email'      => 'max@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Max',
                'last_name'  => 'Rafferty',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->save();
        } catch (PDOException $exception) {
            $this->getOutput()->write(
                'Got Exception When creating max (this might be ok): ' . $exception->getMessage()
            );
        }

        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'jalbers',
                'email'      => 'joni@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Joni',
                'last_name'  => 'Albers',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->save();
        } catch (PDOException $exception) {
            $this->getOutput()->write(
                'Got Exception When creating joni (this might be ok): ' . $exception->getMessage()
            );
        }

        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'manchuck',
                'email'      => 'chuck@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Chuck',
                'last_name'  => 'Reeves',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->save();
        } catch (PDOException $exception) {
            $this->getOutput()->write(
                'Got Exception When creating chuck (this might be ok): ' . $exception->getMessage()
            );
        }
    }
}
