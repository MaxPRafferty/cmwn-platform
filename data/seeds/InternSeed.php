<?php

use Phinx\Seed\AbstractSeed;

class InternSeed extends AbstractSeed
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
        $date        = new \DateTime();
        $expires     = new \DateTime('+3 days');
        $table   = $this->table('users');
        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'agayo',
                'email'        => 'mailto:antoinettejgayo@gmail.com',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Antoinette',
                'last_name'    => 'Gayo',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Antoinette (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'dbalobin',
                'email'        => 'mailto:denis.balobin@columbia.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Denis',
                'last_name'    => 'Balobin',
                'gender'       => 'male',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Denis (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'wlau',
                'email'        => 'winniel@umich.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Winnie',
                'last_name'    => 'Lau',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Wennie (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'lresnahan',
                'email'        => 'leslie_bresnahan@brown.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Leslie',
                'last_name'    => 'Bresnahan',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Leslie (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'amariottini',
                'email'        => 'annalise.mariottini@columbia.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Annalise',
                'last_name'    => 'Mariottini',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Annalise (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'jwei',
                'email'        => 'yaohui.wei@vanderbilt.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Jack',
                'last_name'    => 'Wei',
                'gender'       => 'male',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Jack (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'cyenikapati',
                'email'        => 'cy878@nyu.edu',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Chaithra',
                'last_name'    => 'Yenikapati',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Chaithra (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $table->setData([]);

        try {
            $table->insert([
                'user_id'      => (string) \Ramsey\Uuid\Uuid::uuid1(),
                'username'     => 'nolaya',
                'email'        => 'olaya.natalie18@gmail.com ',
                'code'         => 'business',
                'type'         => 'ADULT',
                'password'     => null,
                'first_name'   => 'Natalie',
                'last_name'    => 'Olaya',
                'gender'       => 'female',
                'meta'         => '[]',
                'birthdate'    => null,
                'created'      => $date->format("Y-m-d H:i:s"),
                'updated'      => $date->format("Y-m-d H:i:s"),
                'code_expires' => $expires->format("Y-m-d H:i:s"),
                'super'        => 0,
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating Natalie (this might be ok): ' . $exception->getMessage()
                );
            }
        }

    }
}
