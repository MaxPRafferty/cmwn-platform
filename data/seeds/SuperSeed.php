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
        $expires = new \DateTime('+2 Weeks');
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'mfafferty',
                'email'      => 'max@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Max',
                'last_name'  => 'Rafferty',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Max: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'jalbers',
                'email'      => 'joni@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Joni',
                'last_name'  => 'Albers',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Joni: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'manchuck',
                'email'      => 'chuck@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Chuck',
                'last_name'  => 'Reeves',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Chuck: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'awalzer',
                'email'      => 'adam@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Adam',
                'last_name'  => 'Walzer',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Adam: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'gotto',
                'email'      => 'gina@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Gina',
                'last_name'  => 'Otto',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Gina: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'vdorazio',
                'email'      => 'valerie@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Valerie',
                'last_name'  => 'D\'Orazio',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Valerie: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'mwren',
                'email'      => 'marilyn@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Marilyn',
                'last_name'  => 'Wren',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Marilyn: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'bzatta',
                'email'      => 'bruno@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'       => 'ADULT',
                'first_name' => 'Bruno',
                'last_name'  => 'Zatta',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Bruno: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'      => \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'nespartinez',
                'email'        => 'nikki@ginasink.com',
                'code'         => 'business',
                'code_expires' => $expires->format('Y-m-d H:i:s'),
                'type'         => 'ADULT',
                'first_name'   => 'Nikki',
                'last_name'    => 'Espartinez',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'super'        => 1,
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Bruno: ' . $exception->getMessage()
                );
            }
        }
    }
}
