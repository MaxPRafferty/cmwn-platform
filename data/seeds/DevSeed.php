<?php

use Phinx\Seed\AbstractSeed;

class DevSeed extends AbstractSeed
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

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
        //$2y$10$uwAA5/gEompK7MybohPVeu/jBFNETD/64FbMOToUPgiR9mAtCRXQq
        $this->faker = Faker\Factory::create();
        $adults = [];

        for ($count = 0; $count < 15; $count++) {
            $adults[] = [
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => $this->faker->unique()->domainWord,
                'email'      => $this->faker->unique()->safeEmail,
                'code'       => null,
                'type'       => 'ADULT',
                'password'   => password_hash('oexvs278', PASSWORD_DEFAULT),
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'gender'     => 'male',  // dont judge me
                'meta'       => '[]',
                'birthdate'  => null,
                'updated'    => (string) $this->faker->dateTimeBetween('-30 years')->getTimestamp(),
                'super'      => 0
            ];
        }

        $this->table('users')
            ->insert($adults)
            ->save();

        $this->faker = Faker\Factory::create();
        $children = [];

        for ($count = 0; $count < 15; $count++) {
            $children[] = [
                'user_id'    => \Ramsey\Uuid\Uuid::uuid1(),
                'username'   => $this->faker->unique()->domainWord,
                'email'      => $this->faker->unique()->safeEmail,
                'code'       => null,
                'type'       => 'CHILD',
                'password'   => password_hash('oexvs278', PASSWORD_DEFAULT),
                'first_name' => $this->faker->firstName,
                'last_name'  => $this->faker->lastName,
                'gender'     => 'male',  // dont judge me
                'meta'       => '[]',
                'birthdate'  => null,
                'updated'    => (string) $this->faker->dateTimeBetween('-13 years')->getTimestamp(),
                'super'      => 0
            ];
        }

        $this->table('users')
            ->insert($children)
            ->save();
    }

    public function seedOrgsAndGroups()
    {
        $orgs = [];

        for ($count = 0; $count < 100; $count++) {
            $orgs[] = [
                'org_id'      => \Ramsey\Uuid\Uuid::uuid1(),
                'title'       => $this->faker->company,
                'description' => $this->faker->catchPhrase,
                'meta'        => '[]',
                'created'     => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'updated'     => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'deleted'     => null,
                'type'        => 'District',
            ];
        }

        $this->table('organizations')
            ->insert($orgs)
            ->save();

        $groups = [];
        for ($count = 0; $count < 100; $count++) {
            $org = $orgs[array_rand($orgs)];

            $groups[] = [
                'group_id'        => \Ramsey\Uuid\Uuid::uuid1(),
                'organization_id' => $org['org_id'],
                'title'           => $this->faker->city,
                'description'     => $this->faker->catchPhrase,
                'meta'            => '[]',
                'lft'             => 1,
                'rgt'             => 4,
                'created'         => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'updated'         => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'deleted'         => null,
                'type'            => 'School'
            ];

            $groups[] = [
                'group_id'        => \Ramsey\Uuid\Uuid::uuid1(),
                'organization_id' => $org['org_id'],
                'title'           => $this->faker->city,
                'description'     => $this->faker->firstName,
                'meta'            => '[]',
                'lft'             => 2,
                'rgt'             => 3,
                'created'         => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'updated'         => (string) $this->faker->dateTimeBetween('-15 days')->getTimestamp(),
                'deleted'         => null,
                'type'            => 'Class'
            ];
        }

        $this->table('groups')
            ->insert($groups)
            ->save();
    }
}
