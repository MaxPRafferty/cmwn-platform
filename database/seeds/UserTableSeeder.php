<?php

use Illuminate\Database\Seeder;
use app\User;
use app\Group;
use app\Organization;
use app\District;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();


        DB::table('district_organization')->delete();
        DB::table('districts')->delete();
        DB::table('organizations')->truncate();

        DB::table('groups')->truncate();
        DB::table('child_guardian')->truncate();
        DB::table('roleables')->truncate();
        DB::table('game_flip')->truncate();
        DB::table('flip_user')->truncate();
        DB::table('flips')->delete();
        DB::table('games')->delete();
        DB::table('users')->delete();

        for ($i = 1; $i <= 5; ++$i) {
            $districts[$i] = District::create(array(
                    'title' => 'Scnhool District ' . rand(100, 900),
                    'description' => 'District: '.$i.$faker->paragraph(1),
                ));

            $this->command->info('District: "'.$districts[$i]->title.'" created!');
        }

        for ($i = 1; $i <= 20; ++$i) {
            $organizations[$i] = Organization::create(array(
                    'title' => 'The ' . $faker->company . ' School',
                    'description' => 'Group: ' . $faker->paragraph(1),
                ));

            $organizations[$i]->districts()->save($districts[rand(1, 4)]);

            $this->command->info('Organization: '.$organizations[$i]->title.' was created!');
        }

        $group_array = ['Science', 'Math', 'Art', 'Chemistry', 'Music', 'Dance', 'Spanish', 'French', 'English', 'Language Arts', 'Pre-Algebra', 'Geometry', 'Woodshop', 'Drama', 'Grammar', 'Yearbook', 'Painting', 'Sculpture', 'Ceramics', 'Pottery', 'Band', 'Physics', 'Geology', 'Environmental Science', 'Calculus', 'Social Studies', 'US History', 'Sociology', 'Gymnastics'];

        for ($i = 1; $i <= 100; ++$i) {
            $groups[$i] = Group::create(array(
                    'organization_id' => $organizations[rand(1, 19)]->id.$i,
                    'title' => $group_array[array_rand($group_array)] . ' ' . rand(1, 3) . '0' . rand(1, 9),
                    'description' => 'Class Description: ' . $faker->paragraph(1),
                ));

            $this->command->info('Group: "'. $groups[$i]->title .'" created!');
        }

        // Create Users
        $this->command->info('Site Admins!');
        $users[] = User::create(array(
                'first_name' => 'Jon',
                'last_name' => 'Toshmatov',
                'type' => 1,
                'username' => 'toshmatov',
                'gender' => 'male',
                'email' => 'jontoshmatov@yahoo.com',
                'password' => Hash::make('business'),
                'student_id' => 'jontoshmatov@yahoo.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Arron',
                'last_name' => 'Kallenberg',
                'type' => 1,
                'username' => 'kallena',
                'gender' => 'male',
                'email' => 'arron.kallenberg@gmail.com',
                'password' => Hash::make('business'),
                'student_id' => 'arron.kallenberg@gmail.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Cathy',
                'last_name' => 'Sinoradzki',
                'type' => 0,
                'username' => 'c.sinoradzki',
                'gender' => 'female',
                'email' => 'cathy@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'cathy@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Emily',
                'last_name' => 'McLean',
                'type' => 0,
                'username' => 'e.mclean',
                'gender' => 'female',
                'email' => 'emily@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'emily@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Gina',
                'last_name' => 'Otto',
                'type' => 0,
                'username' => 'g.otto',
                'gender' => 'female',
                'email' => 'gina@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'gina@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Janette',
                'last_name' => 'Barber',
                'type' => 0,
                'username' => 'j.barber',
                'gender' => 'female',
                'email' => 'janette@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'janette@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Joni',
                'last_name' => 'Albers',
                'type' => 0,
                'username' => 'j.albers',
                'gender' => 'female',
                'email' => 'joni@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'joni@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Max',
                'last_name' => 'Rafferty',
                'type' => 0,
                'username' => 'tonyhamburger@hashtagxtreme92',
                'gender' => 'male',
                'email' => 'max@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'max@ginasink.com',
            ));

        $users[] = User::create(array(
                'first_name' => 'Micah',
                'last_name' => 'Rolon',
                'type' => 0,
                'username' => 'm.rolon',
                'gender' => 'male',
                'email' => 'micah@ginasink.com',
                'password' => Hash::make('Cmwn2015'),
                'student_id' => 'micah@ginasink.com',
            ));

        $this->command->info('Creating superintendents!');
        $superintendents = $this->createUsers(20, $faker);

        foreach ($superintendents as $superintendent) {
            $superintendent->districts()->save($districts[rand(1, 5)], array('role_id' => rand(1, 2)));
        }

        $this->command->info('Creating principals!');
        $principals = $this->createUsers(100, $faker);

        foreach ($principals as $principal) {
            $principal->organizations()->save($organizations[rand(1, 20)], array('role_id' => rand(1, 2)));
        }

        $this->command->info('Creating teachers!');
        $teachers = $this->createUsers(100, $faker);

        foreach ($teachers as $teacher) {
            $teacher->groups()->save($groups[rand(1, 100)], array('role_id' => 1));
        }

        $this->command->info('Creating kids!');
        $kids = $this->createUsers(200, $faker);

        foreach ($kids as $kid) {
            $kid->groups()->save($groups[rand(1, 100)], array('role_id' => 3));
        }
    }

    private function createUsers($count, $faker)
    {
        for ($i = 1; $i <= $count; ++$i) {
            $first_name = $faker->firstName;
            $last_name = $faker->lastName;

            $email = strtolower($first_name.'.'.$last_name.'@'.$faker->safeEmailDomain);

            $users[$i] = User::create(array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'username' => str_slug($first_name . '_' . $last_name),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'birthdate' => $faker->dateTimeBetween('-40 years', '-8 years'),
                    'email' => $email,
                    'password' => Hash::make('business'),
                    'student_id' => $faker->uuid,
                ));

            //$this->command->info($frist_name.' '.$last_name.' created!');
        }

        return $users;
    }
}
