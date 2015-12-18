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
    protected static $district_id;
    protected static $organization_id;
    protected static $DATA;

    public static function migratecsv()
    {
        self::$file = base_path('storage/app/'.self::$data['file']);
        $output = array();

        //try {
            self::$sheetname = \Excel::load(self::$file)->getSheetNames();
            \Excel::load(self::$file, function ($reader) {
                $sheet = '';

                foreach ($reader->toArray() as $sheet => $row) {

                    if (self::$sheetname[$sheet] == 'Students') {
                        self::$DATA['Students'] = $row;
                    }
                    if (self::$sheetname[$sheet] == 'Classes') {
                        self::$DATA['Classes'] = $row;
                    }
                    if (self::$sheetname[$sheet] == 'Teachers') {
                        self::$DATA['Teachers'] = $row;
                    }
                }


                foreach(self::$DATA['Students'] as $row=>$data){
                    $org_id = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
                    if ($org_id[1]) {
                        $organization = Organization::where('code', $org_id[1])->lists('uuid')->toArray();
                        if(empty($organization)){
                            dd("Organization not found"); //@TODO: error out here
                            break;
                        }
                        $organization_uuid = $organization[0];
                        if (self::$data['parms']['organization_id'] != $organization_uuid) {
                            dd("wrong organization"); //@TODO: error out here
                        }
                        self::$organization_id = $organization_uuid;
                        break;
                    }
                    break;
                }
            });


            foreach(self::$DATA['Students'] as $data){
               echo "Students ";
               if($data['ddbnnn']){
                   var_dump(self::updateStudents($data));
               }
            }

            foreach(self::$DATA['Classes'] as $data){
                echo "Classes";
                if ($data['offical_class']) {
                    var_dump(self::updateClasses($data));
                }
            }

            foreach(self::$DATA['Teachers'] as $data){
                echo "Teachers";
                if($data['person_type']) {
                    var_dump(self::updateStudents($data));
                }
            }


            return true;
        //} catch (\Exception $e) {
            //dd('Houston, we have a problem: '.$e->getMessage());
        //}
    }

    protected static function updateClasses($data)
    {
        $organization_id = self::$organization_id;
        $group = Group::where('organization_id', '=',  $organization_id)->where('class_number', '=', $data['class_number']);
        //$group = Group::updateOrCreate(['organization_id'=>$organization_id], ['title'=>$data['offical_class']]);
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
    }

    protected static function updateTeachers($data)
    {
        $role_id = 3;
        $error = array();

        //adding teachers to users table
        foreach ($data as $title => $val) {
            $teacher_id = $data['person_type'].' '.$data['first_name'].' '.$data['middle_name'].' '.$data['last_name'];
            $teacher_id = str_slug($teacher_id);
            $teachers = User::where('student_id','staff-'.$teacher_id)->where('username',$teacher_id.'@changemyworld.com');
            //Adding a new teacher
            if (!$teachers->count()){
                $teachers = new User();
                $teachers->student_id = 'staff-'.$teacher_id;
                $teachers->username = $teacher_id.'@changemyworld.com';
                $teachers->first_name = $data['first_name'];
                $teachers->middle_name = $data['middle_name'];
                $teachers->last_name = $data['last_name'];
                $teachers->gender = $data['gender'];
                $teachers->email = $data['email_address'];
                $output = $teachers->save();
                $uuid = User::where('id',$teachers->uuid)->lists('uuid')->toArray();
                $uuid = $uuid[0];
            }else {
                $teachers = User::where('student_id','staff-'.$teacher_id)->where('username',$teacher_id.'@changemyworld.com')->first();
                $output = $teachers->update([
                    'student_id' => 'staff-'.$teacher_id,
                    'username' => $teacher_id.'@changemyworld.com',
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
                $role_id = 1;
            }

            if ($data['person_type']=='Teacher'){
                $role_id = 2;
            }

            //Assigning teachers to main class only
            $group_uuid = null;
            if($data['class_number']) {
                $group_uuid = Group::where('class_number', $data['class_number'])->lists('uuid')->toArray();

                $teachers->groups()->sync(array(
                    $uuid => array('user_id' => $uuid, 'roleable_id' => $group_uuid[0], 'role_id' => $role_id)
                ));
            }





            //Assigning teachers to cluster classes
/*            if($data['class_number']){
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
            }*/

            return true;
        }
        return true;
    }

    protected static function updateStudents($data)
    {
                $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
                $district_uuid = self::updateStudentsDistricts($data);
                $organization_uuid = self::updateStudentsOrganizations($data, $district_uuid);
                $group_uuid = self::updateStudentsGroups($data, $organization_uuid);
                $student = self::updateStudentsData($data);
                return $DDBNNN[1];
    }

    protected static function updateStudentsDistricts($data){
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        $district = District::firstOrNew(['title'=>$DDBNNN[0]], ['system_id' => 1]);
        $district->title = $DDBNNN[0];
        $district->description = $DDBNNN[0];
        $district->system_id = 1;
        $district->save();
        $district_uuid = $district->uuid;
        if (is_int($district->uuid)){
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
            $output['distr_org'] = $organization->districts()->sync(array($district_uuid));

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
        $user = User::firstOrNew(['student_id'=>$data['student_id']]);
        $username = $data['first_name']."-".$data['student_id']."-".$data['last_name'];
        $username = str_slug($username)."@changemyworldnow.com";
        $user->username = $username;
        $user->student_id = $data['student_id'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->gender = $data['sex'];
        $user->birthdate = $data['birth_dt'];
        $user->save();
        $student_uuid = $user->uuid;
        if (is_int($student_uuid)) {
            $uuid = User::where('id', $user->uuid)->lists('uuid')->toArray();
            $student_uuid = $uuid[0];
        }


        //Add parents
        if ($data['adult_first_1'] && $data['adult_last_1']){
            $username = $data['adult_first_1']." ".$data['adult_last_1'];
            $username = str_slug($username)."@changemyworldnow.com";
            $parent_id = $data['adult_first_1']." ".$data['adult_last_1'];
            $parent_id = str_slug($parent_id)."@changemyworldnow.com";
            $parent = User::firstOrNew(['student_id'=>$parent_id, 'username'=>$username]);
            $parent->username = $username;
            $parent->student_id = $parent_id;
            $parent->first_name = $data['adult_first_1'];
            $parent->last_name = $data['adult_last_1'];
            $parent->save();
            $parent_uuid = $parent->uuid;
            if (is_int($parent_uuid)) {
                $uuid = User::where('id', $parent->uuid)->lists('uuid')->toArray();
                $parent_uuid = $uuid[0];
            }
        }

            $user->guardians()->sync([$parent_uuid],[$student_uuid]);
            $user->guardianReference()->sync([$student_uuid]);

        if(!$user->guardiansall->contains($parent_uuid)) {
            $user->guardiansall()->sync(array(
                $student_uuid => array(
                    'user_id' => $parent_uuid,
                    'student_id' => $student_uuid,
                    'first_name' => $data['adult_first_1'],
                    'last_name' => $data['adult_last_1'],
                    'phone' => $data['adult_phone_1']
                )
            ));
        }


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
