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

                            if (!empty($row['offical_class'])) {
                                $output['Classes'] = self::updateClasses($row);
                            }
                        }
                    }
                    //Saving the teachers into users
                    if (self::$sheetname[$sheet] == 'Teachers') {
                        foreach ($data[$sheet] as $row) {

                            if (!empty($row['person_type'])) {
                                $output['Teachers'] = self::updateTeachers($row);
                            }
                        }
                    }
                    //Saving the Students into users
                    if (self::$sheetname[$sheet] == 'Students') {
                        foreach ($data[$sheet] as $i=>$row) {
                            if (!empty($row['ddbnnn'])) {
                               $output['Students'] = self::updateStudents($row);
                            }
                        }
                    }
                }

            });

            dd($output);
        } catch (\Exception $e) {
            dd('Houston, we have a problem: '.$e->getMessage());
        }
    }

    protected static function updateClasses($data)
    {
        $organization_id = self::$data['parms']['organization_id'];
        $group = Group::where('organization_id', '=',  $organization_id)->where('title', '=', $data['offical_class']);

        if(!$group->get()->count()){
            $group = new Group();
            $group->organization_id = $organization_id;
            $group->title = $data['offical_class'];
            $group->class_number = $data['class_number'];
            $group->cluster_class = $data['sub_class_number'];
            $output['Group'][] = $group->save();
            $uuid = Group::where('id',$group->uuid)->lists('uuid')->toArray();

            $group_uuid = $uuid[0];
        }else{
            $group = $group->first();
            $group->organization_id = $organization_id;
            $group->title = $data['offical_class'];
            $group->class_number = $data['class_number'];
            $group->cluster_class = $data['sub_class_number'];
            $output['Group'][] = $group->save();
            $group_uuid = $group->uuid;
        }

        return $output;
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

            //Assigning teachers to main class
            $teachers->groups()->sync(array(
                $uuid=>array('user_id'=>$uuid,'roleable_id'=>$data['class_number'], 'role_id'=>$role_id)
            ));

            //Assigning teachers to cluster classes
            if($data['class_number']){
                $cluster_class = Group::where('class_number', $data['class_number'])->lists('cluster_class')->toArray();
                foreach($cluster_class as $class){
                    $classes = explode(';',$class);
                    foreach($classes as $cls){
                        $teachers->groups()->attach(array(
                            $uuid=>array('user_id'=>$uuid,'roleable_id'=>$cls, 'role_id'=>$role_id)
                        ));
                    }
                    break;
                }

            }

            return true;
        }
        return true;
    }

    protected static function updateStudents($data)
    {
                //creating or updating districts
                $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
                $district_uuid = self::updateStudentsDistricts($data);
                $organization_uuid = self::updateStudentsOrganizations($data, $district_uuid);
                $group_uuid = self::updateStudentsGroups($data, $organization_uuid);
                $student = self::updateStudentsData($data);
                return 'Success';
    }



    protected static function updateStudentsDistricts($data){
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        $district = District::firstOrCreate(array('title'=>$DDBNNN[0], 'system_id' => 1));
        $district->title = $DDBNNN[0];
        $district->system_id = 1;
        $district->save();
        $district_uuid = $district->uuid;
        if (gettype($district->uuid=='integer')){
            $uuid = District::where('id',$district->uuid)->lists('uuid')->toArray();
            $district_uuid = $uuid[0];
        }
        return $district_uuid;
    }

    protected static function updateStudentsOrganizations($data, $district_uuid){
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        //Adding Organizations
        $organization = Organization::where(['code' => $DDBNNN[1]])
            ->with(array('districts' => function ($query) use ($district_uuid) {
                $query->where('district_id', $district_uuid);
            }));

        if(!$organization->count()){
            $organization = new Organization();
            $organization->code = $DDBNNN[1];
            $organization->title = $DDBNNN[1];
            $output['Organization'] = $organization->save();
            $uuid = $organization->uuid;
            $uuid = Organization::where('id',$organization->uuid)->lists('uuid')->toArray();
            $organization_uuid = $uuid[0];
        }else{
            $organization = $organization->first();
            $organization->description = 'updated';
            $output['Organization'] = $organization->save();
            $uuid = $organization->uuid;
            $organization_uuid = $uuid;

        }
        if (!$organization->districts->contains($district_uuid)) {
            $output['distr_org'] = $organization->districts()->sync(array($district_uuid));
        }
        return $organization_uuid;
    }

    protected static function updateStudentsGroups($data, $organization_uuid){
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        //Adding groups
        $group = Group::where('organization_id', '=', $organization_uuid);

        if(!$group->count()){
            $group = new Group();
            $group->organization_id = $organization_uuid;
            $group->title = $data['off_cls'];
            $output['Group'] = $group->save();
            $uuid = Group::where('id',$group->uuid)->lists('uuid')->toArray();
            $group_uuid = $uuid[0];
        }else{
            $group = $group->first();
            $group->organization_id = $organization_uuid;
            $group->title = $data['off_cls'];
            $output['Group'] = $group->save();
            $group_uuid = $group->uuid;
        }
        return $group_uuid;
    }

    protected static function updateStudentsData($data){
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        //Adding students

        $user = User::where('student_id', '=', $data['student_id']);
        $username = $data['first_name']."-".$data['student_id']."-".$data['last_name']."@changemyworldnow.com";

        if($user->get()->count()==0){
            $user = new User();
            $user->username = $username;
            $user->student_id = $data['student_id'];
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->gender = $data['sex'];
            $user->birthdate = $data['birth_dt'];
            $output['Students'] = $user->save();
            $uuid = User::where('id',$user->uuid)->lists('uuid')->toArray();
            $child_uuid = $uuid[0];

        }else{
            $user = $user->first();
            $user->username = $username;
            $user->student_id = $data['student_id'];
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->gender = $data['sex'];
            $user->birthdate = $data['birth_dt'];
            $output['Students'] = $user->save();
            $child_uuid = $user->uuid;
        }

        $output['guardianReference'] = array();
        $test = $user->guardianReference->contains($child_uuid);
        if (!$test){
            return false;
        }
        $output['guardianReference'] = $user->guardianReference()->attach(array(
            $uuid=>array(
                'user_id'=>$child_uuid,
                'first_name'=>$data['adult_first_1'],
                'last_name'=>$data['adult_last_1'],
                'phone'=>$data['adult_phone_1']
            )));
    }

    protected static function mailNotification($data){
        //@TODO email notification has been temporarily disabled. JT 10/11
        return false;
        $notifier = new Notifier();
        $notifier->to = Auth::user()->email;
        $notifier->subject = 'Your import is completed at '.date('m-d-Y h:i:s A');
        $notifier->template = 'emails.import';
        $notifier->attachData(['user' => Auth::user()]);
        $notifier->send();
    }
}
