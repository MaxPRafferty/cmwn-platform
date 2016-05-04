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
                'username'   => 'csinoradzki',
                'email'      => 'cathy@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Cathy',
                'last_name'  => 'Snooteriski',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Cathy: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'jfontaina',
                'email'      => 'jasmine@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Jasmine',
                'last_name'  => 'Fontaina',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Jasmine: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'awalzer',
                'email'      => 'adam@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Adam',
                'last_name'  => 'Walzer',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
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
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Gina',
                'last_name'  => 'Otto',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
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
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Valerie',
                'last_name'  => 'D\'Orazio',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
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
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Marilyn',
                'last_name'  => 'Wren',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
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
                'username'   => 'emclean',
                'email'      => 'emily@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Emily',
                'last_name'  => 'Mclean',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Emily: ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);
        try {
            $table->insert([
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => 'jperriott',
                'email'      => 'jackie@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Jackie',
                'last_name'  => 'Perriott',
                'gender'     => 'female',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 1
            ])
                ->saveData();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When inserting Jackie: ' . $exception->getMessage()
                );
            }
        }
    }
}
