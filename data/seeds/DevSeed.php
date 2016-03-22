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
        try {
            $userTable = $this->table('users');
            $teacherId = (string) \Ramsey\Uuid\Uuid::uuid1();
            $userTable->insert([
                'user_id'    => $teacherId,
                'username'   => 'teacher',
                'email'      => 'teacher@ginasink.com',
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('business', PASSWORD_DEFAULT),
                'first_name' => 'Excited',
                'last_name'  => 'Puffin',
                'gender'     => 'male',
                'meta'       => '[]',
                'birthdate'  => null,
                'created'    => $date->format("Y-m-d H:i:s"),
                'updated'    => $date->format("Y-m-d H:i:s"),
                'super'      => 0
            ])
                ->save();

            $org   = $this->table('organizations');
            $orgId = (string) \Ramsey\Uuid\Uuid::uuid1();
            $org->insert([
                'org_id'      => $orgId,
                'title'       => 'Ginas Ink',
                'description' => null,
                'meta'        => '[]',
                'created'     => $date->format("Y-m-d H:i:s"),
                'updated'     => $date->format("Y-m-d H:i:s"),
                'type'        => 'district'
            ])
                ->save();

            $group   = $this->table('groups');
            $groupId = (string) \Ramsey\Uuid\Uuid::uuid1();

            $group->insert([
                'group_id'        => $groupId,
                'organization_id' => $orgId,
                'title'           => 'Ginas school',
                'description'     => null,
                'meta'            => '[]',
                'lft'             => 1,
                'rgt'             => 2,
                'created'         => $date->format("Y-m-d H:i:s"),
                'updated'         => $date->format("Y-m-d H:i:s"),
            ])->save();


            $userGroups = $this->table('user_groups');
            $userGroups->insert([
                'user_id' => $teacherId,
                'group_id' => $groupId,
                'role'     => 'admin',
            ])->save();
        } catch (PDOException $exception) {
            $this->getOutput()->write(
                'Got Exception When creating teacher, district and class (this might be ok): ' . $exception->getMessage()
            );
        }
    }
}
