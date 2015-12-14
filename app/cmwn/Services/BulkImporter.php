<?php

namespace app\cmwn\Services;

use app\Group;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\District;
use app\Organization;
use app\User;
use Illuminate\Support\Facades\Auth;

class BulkImporter
{
    use DispatchesJobs;
    public static $data;
    protected static $sheetname;
    protected static $file;

    public static function migratecsv()
    {
        self::$file = base_path('storage/app/'.self::$data['file']);
        $output = array();

        try {
            self::$sheetname = \Excel::load(self::$file)->getSheetNames();

            \Excel::load(self::$file, function ($reader) {
                $sheet = '';
                foreach ($reader->toArray() as $sheet => $row) {
                    $data[$sheet] = $row;
                    //Saving the Classes into groups
                    if (self::$sheetname[$sheet] == 'Classes') {
                        foreach ($data[$sheet] as $row) {
                            //$output['Classes'] = self::updateClasses($row);
                        }
                    }
                    //Saving the teachers into users
                    if (self::$sheetname[$sheet] == 'Teachers') {
                        foreach ($data[$sheet] as $row) {

                            if (!empty($row['person_type'])) {
                                //$output['Teachers'] = self::updateTeachers($row);
                            }
                        }
                    }
                    //Saving the Students into users
                    if (self::$sheetname[$sheet] == 'Students') {
                        foreach ($data[$sheet] as $row) {
                            $output['Students'] = self::updateDB($row);
                        }
                    }
                }

            });

            dd($output);
        } catch (\Exception $e) {
            dd('Houston, we have a problem: '.$e->getMessage());
        }
    }

    public static function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    protected static function updateDB($data)
    {
        foreach ($data as $title => $val) {
            if ($data['student_id'] != '') {

                //creating or updating districts
                $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);

                //Adding Districts
                $district = District::firstOrCreate(['title' => $DDBNNN[0], 'system_id' => 1]);
                $district->code = $DDBNNN[0];
                $district->system_id = 1;
                $district->title = $DDBNNN[0];
                $output = $district->save();
                
                $uuid = District::where('id',$district->uuid)->lists('uuid')->toArray();
                $district_uuid = $uuid[0];
                dd($district_uuid);

                //Adding Organizations
                $organization = Organization::where(['code' => $DDBNNN[1]])
                                ->with(array('districts' => function ($query) use ($district_uuid) {
                                                                $query->where('district_id', $district_uuid);
                                                            }))->first();

                if (is_null($organization)) {
                    $organization = new Organization();
                }

                $organization->code = $DDBNNN[1];
                $organization->title = $DDBNNN[1];
                $organization->save();
                $uuid = Organization::where('id',$organization->uuid)->lists('uuid')->toArray();
                $organization_uuid = $uuid[0];

                if (!$organization->districts->contains($district_uuid)) {
                    $organization->districts()->attach($district_uuid);
                }

                //Adding groups
                $group = Group::firstOrCreate(['organization_id' => $organization_uuid]);
                $group->title = $data['off_cls'];
                $group->save();

                //Adding students
                $user = User::firstOrCreate(['student_id' => $data['student_id']]);
                $user->student_id = $data['student_id'];
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->gender = $data['sex'];
                $user->birthdate = $data['birth_dt'];
                $user->save();
                $child_id = $user->uuid;

                $guardian = \DB::table('guardian_reference')
                    ->where('student_id', '=', $data['student_id'])
                    ->where('first_name', '=', $data['adult_first_1'])
                    ->where('last_name', '=', $data['adult_last_1'])
                    ->get();

                if (isset($guardian[0]->uuid)) {
                    $output = \DB::table('guardian_reference')->where('id', $guardian[0]->uuid)
                       ->update(array(
                           'student_id' => $data['student_id'],
                           'first_name' => $data['adult_first_1'],
                           'last_name' => $data['adult_last_1'],
                           'phone' => $data['adult_phone_1'],
                       ));
                } else {
                    $output = \DB::table('guardian_reference')->insert(array(
                        'student_id' => $data['student_id'],
                        'first_name' => $data['adult_first_1'],
                        'last_name' => $data['adult_last_1'],
                        'phone' => $data['adult_phone_1'],
                   ));
                }
            }
        }

        //@TODO email notification has been temporarily disabled. JT 10/11
        return false;
        $notifier = new Notifier();
        $notifier->to = Auth::user()->email;
        $notifier->subject = 'Your import is completed at '.date('m-d-Y h:i:s A');
        $notifier->template = 'emails.import';
        $notifier->attachData(['user' => Auth::user()]);
        $notifier->send();
    }

    protected static function updateTeachers($data)
    {
        $role_id = 3;

        //adding teachers to users table
        foreach ($data as $title => $val) {
            $student_id = $data['person_type'].' '.$data['first_name'].' '.$data['middle_name'].' '.$data['last_name'];
            $student_id = str_slug($student_id);
            $teachers = User::where('student_id','staff-'.$student_id)->where('username',$student_id.'@changemyworld.com');
            //Adding a new teacher
            if (!$teachers->count()){
                $teachers = new User();
                $teachers->student_id = 'staff-'.$student_id;
                $teachers->username = $student_id.'@changemyworld.com';
                $teachers->first_name = $data['first_name'];
                $teachers->middle_name = $data['middle_name'];
                $teachers->last_name = $data['last_name'];
                $teachers->gender = $data['gender'];
                $teachers->email = $data['email_address'];
                $output = $teachers->save();
                $uuid = User::where('id',$teachers->uuid)->lists('uuid')->toArray();
                $uuid = $uuid[0];
            }else {
                $teachers = User::where('student_id','staff-'.$student_id)->where('username',$student_id.'@changemyworld.com')->first();
                $output = $teachers->update([
                    'student_id' => 'staff-'.$student_id,
                    'username' => $student_id.'@changemyworld.com',
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email_address'],
                    'gender' => $data['gender']
                ]);
                $teachers->save();
                $uuid = $teachers->uuid;
            }


            //Assigning the teacher to class
            if ($data['person_type']=='Principal'){
                $role_id = 1;
            }
            if ($data['person_type']=='Assistant Principal'){
                $role_id = 2;
            }

            if ($data['person_type']=='Teacher'){
                $role_id = 2;
            }

            $teachers->groups()->sync(array(
                $uuid=>array('user_id'=>$uuid,'roleable_id'=>$data['class_number'], 'role_id'=>$role_id)
            ));

            return true;
        }
        return true;
    }

    protected static function updateClasses($data)
    {
        $organization_id = self::$data['parms']['organization_id'];
        foreach ($data as $title => $val) {
            $group = Group::firstOrCreate(['organization_id' => $organization_id, 'title' => $data['offical_class']]);
            $group->organization_id = $organization_id;
            $group->title = $data['offical_class'];
            $group->description = $data['class_number'];
            $group->save();
        }

        return true;
    }
}
