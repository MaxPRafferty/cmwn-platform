<?php

use Phinx\Seed\AbstractSeed;

class DevSeed extends AbstractSeed
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
        $date = new \DateTime();
        $userTable = $this->table('users');
        $principalId = (string) \Ramsey\Uuid\Uuid::uuid1();
        try {
            $userTable->insert([
                'user_id'    => $principalId,
                'username'   => 'principal',
                'email'      => 'principal@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Gina',
                'last_name'  => 'Principal',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 0
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating principal (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $userTable->setData([]);
        $teacherId = (string) \Ramsey\Uuid\Uuid::uuid1();
        try {
            $userTable->insert([
                'user_id'    => $teacherId,
                'username'   => 'teacher',
                'email'      => 'teacher@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Gina',
                'last_name'  => 'Teacher',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 0
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating teacher (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $studentId = (string) \Ramsey\Uuid\Uuid::uuid1();
        $userTable->setData([]);
        try {
            $userTable->insert([
                'user_id'    => $studentId,
                'username'   => 'student',
                'email'      => 'student@ginasink.com',
                'code'       => null,
                'type'       => 'CHILD',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Gina',
                'last_name'  => 'Student',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 0
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating student (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $org   = $this->table('organizations');
        $orgId = '9ee13654-0288-11e6-a70a-0800274f2cef';
        try {
            $org->insert([
                'org_id'      => $orgId,
                'title'       => 'Ginas Ink',
                'description' => null,
                'meta'        => '[]',
                'created'     => $date->format("Y-m-d H:i:s"),
                'updated'     => $date->format("Y-m-d H:i:s"),
                'type'        => 'district'
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating district (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $group   = $this->table('groups');
        $schoolId = '9ee14a04-0288-11e6-8625-0800274f2cef';
        try {
            $group->insert([
                'group_id'        => $schoolId,
                'organization_id' => $orgId,
                'title'           => 'Ginas school',
                'description'     => null,
                'meta'            => '[]',
                'head'             => 1,
                'tail'             => 4,
                'created'         => $date->format("Y-m-d H:i:s"),
                'updated'         => $date->format("Y-m-d H:i:s"),
                'type'            => 'school'
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating school (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $group->setData([]);
        $classId = '9ee15bf2-0288-11e6-8b6b-0800274f2cef';
        try {
            $group->insert([
                'group_id'        => $classId,
                'organization_id' => $orgId,
                'title'           => 'Ginas Class',
                'description'     => null,
                'meta'            => '[]',
                'head'            => 2,
                'tail'            => 3,
                'parent_id'       => $schoolId,
                'created'         => $date->format("Y-m-d H:i:s"),
                'updated'         => $date->format("Y-m-d H:i:s"),
                'type'            => 'class'
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeln(
                    'Got Exception When creating class (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $userGroups = $this->table('user_groups');
        try {
            $userGroups->insert([
                'user_id'  => $principalId,
                'group_id' => $schoolId,
                'role'     => 'principal',
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When assigning principal to school (this might be ok): ' . $exception->getMessage()
                );
            }
        }

        $userGroups->setData([]);
        try {
            $userGroups->insert([
                'user_id'  => $teacherId,
                'group_id' => $classId,
                'role'     => 'teacher',
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When assigning teacher to class (this might be ok): ' . $exception->getMessage()
                );
            }
        }
        
        $userGroups->setData([]);
        try {
            $userGroups->insert([
                'user_id'  => $teacherId,
                'group_id' => $studentId,
                'role'     => 'student',
            ])->save();
        } catch (PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    'Got Exception When assigning student to class (this might be ok): ' . $exception->getMessage()
                );
            }
        }

    }
}
