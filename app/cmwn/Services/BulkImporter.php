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
    public $data;
    protected $sheetname;
    protected $excel;
    protected $district_id;
    protected $organization_id;
    protected $DATA;
    protected $errors;

    public function migratecsv()
    {
        $file_path = base_path('storage/app/'.$this->data['file']);

        $this->excel = \Excel::load($file_path);

        var_dump($this->excel->dd());

        $this->getSheets();

        exit();
    }

    public function getSheets()
    {
        $sheetNames = $this->excel->getSheetNames();

        $target = array('Classes', 'Teachers', 'Students');

        if (count(array_intersect($sheetNames, $target)) == count($target)) {
            Excel::selectSheets('sheet1', 'sheet2')->load();
            Excel::selectSheets('sheet1', 'sheet2')->load();
            Excel::selectSheets('sheet1', 'sheet2')->load();
        } else {
            $this->addError('Missing Sheet');
        }
    }

        // self::$file = base_path('storage/app/' . $this->$data['file']);
        // $output = array();

        // $this->addError("Error report for importing the excel spreadsheet.\nSubmitted on ".date('Y-m-d h:i:s A').' by user: '.Auth::user()->student_id."\n Errors:");

        // if (!self::$data['parms']['organization_id']) {
        //     self::$errors[] = "migratecsv: Please select the organization.\n";
        //     self::createReport();
        //     dd('Please select the organization');
        // }

        // self::$sheetname = \Excel::load($this->$file)->getSheetNames();

        // \Excel::load($this->$file, function ($reader) {
        //     $sheet = '';

        //     foreach ($reader->toArray() as $sheet => $row) {
        //         if (self::$sheetname[$sheet] == 'Students') {
        //             self::$DATA['Students'] = $row;
        //         }
        //         if (self::$sheetname[$sheet] == 'Classes') {
        //             self::$DATA['Classes'] = $row;
        //         }
        //         if (self::$sheetname[$sheet] == 'Teachers') {
        //             self::$DATA['Teachers'] = $row;
        //         }
        //     }

        //     foreach (self::$DATA['Students'] as $row => $data) {
        //         $org_id = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        //         if ($org_id[1]) {
        //             $organization = Organization::where('code', $org_id[1])->lists('id')->toArray();

        //             if (empty($organization)) {
        //                 self::$errors .= "migratecsv: Organization $org_id[1] is not found in the DB.\n";
        //                 self::createReport();
        //                 dd(self::$errors);
        //                 break;
        //             }

        //             $organization_id = $organization[0];
        //             if (self::$data['parms']['organization_id'] != $organization_id) {
        //                 $org_not_found = self::$data['parms']['organization_id'];
        //                 self::$errors .= "migratecsv: Organization $org_not_found is not matched with DB.\n";
        //                 self::createReport();
        //                 dd(self::$errors);
        //             }
        //             self::$organization_id = $organization_id;
        //             break;
        //         }
        //         break;
        //     }
        // });

        // foreach (self::$DATA['Students'] as $data) {
        //     if ($data['ddbnnn']) {
        //         self::updateStudents($data);
        //     }
        // }

        // foreach (self::$DATA['Classes'] as $data) {
        //     if ($data['off_cls'] && $data['title']) {
        //         self::updateClasses($data);
        //     }
        // }

        // foreach (self::$DATA['Teachers'] as $data) {
        //     if ($data['person_type']) {
        //         self::updateTeachers($data);
        //     }
        // }

        // $this->addError('End of the error report.');

        // self::createReport();

        // return true;
    //}

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    protected static function updateClasses($data)
    {
        $organization_id = self::$organization_id;
        $group = Group::firstOrNew(['organization_id' => $organization_id, 'class_number' => $data['off_cls']]);
        $group->organization_id = $organization_id;
        $group->title = $data['title'];
        $group->class_number = $data['off_cls'];
        $group->cluster_class = $data['sub_classes'];
        if (!$group->save()) {
            self::$errors .= "updateClasses: Inserting a new class {$data['title']} has failed \n";
        }
        $group_id = (!$group->id) ? $group->uuid : $group->id;
    }

    protected static function updateTeachers($data)
    {
        $role_id = 3;
        $error = array();

            //adding teachers to users table

            $teacher_id = $data['person_type'].' '.$data['first_name'].' '.$data['middle_name'].' '.$data['last_name'];
        $teacher_id = str_slug($teacher_id);
        $username = $teacher_id.'@changemyworld.com';
        $teacher_id = 'staff-'.$teacher_id;
        $teachers = User::firstOrNew(['student_id' => $teacher_id, 'username' => $username]);
        $teachers->student_id = $teacher_id;
        $teachers->username = $username;
        $teachers->first_name = $data['first_name'];
        $teachers->middle_name = $data['middle_name'];
        $teachers->last_name = $data['last_name'];
        $teachers->gender = $data['gender'];
        $teachers->email = $data['email_address'];
        if (!$teachers->save()) {
            self::$errors .= "updateTeachers: Inserting/Updating a techer $teacher_id has failed \n";
        }
        $teacher_id = (!$teachers->id) ? $teachers->uuid : $teachers->id;
            //Assigning the teacher to class
            if ($data['person_type'] == 'Principal') {
                $role_id = 1;
            }
        if ($data['person_type'] == 'Assistant Principal') {
            $role_id = 1;
        }

        if ($data['person_type'] == 'Teacher') {
            $role_id = 2;
        }

            //Assigning teachers to main class only
            $group_id = null;
        if (isset($data['off_cls'])) {
            $group_id = Group::where('class_number', $data['off_cls'])->lists('id')->toArray();
            if ($group_id) {
                if ($group_id && $teacher_id) {
                    $teachers->uuid = $teacher_id;
                    $teachers->groups()->sync(
                            array(
                                $teacher_id => array('roleable_id' => $group_id[0], 'role_id' => $role_id),
                            )
                        );
                }
            }
        }
    }

    protected static function updateStudents($data)
    {
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        $district_id = self::updateStudentsDistricts($data); //Fixed and tested by JT 12/18
            $organization_id = self::updateStudentsOrganizations($data, $district_id); //Fixed and tested by JT 12/18
            $group_id = self::updateStudentsGroups($data);
        $student = self::updateStudentsData($data);

        return true;
    }

    protected static function updateStudentsDistricts($data)
    {
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        $district = District::firstOrNew(['title' => $DDBNNN[0]], ['system_id' => 1]);
        $district->title = $DDBNNN[0];
        $district->description = $DDBNNN[0];
        $district->system_id = 1;
        if (!$district->save()) {
            self::$errors .= "updateStudentsDistricts: Inserting a new district $DDBNNN[0] has failed \n";
        }

        return $district->id;
    }

    protected static function updateStudentsOrganizations($data, $district_id)
    {
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
        $organization = Organization::firstOrNew(['code' => $DDBNNN[0]])->with(array('districts' => function ($query) use ($district_id) {
                $query->where('district_id', $district_id);
            }))->first();
        $organization->code = $DDBNNN[1];
        $organization->title = $DDBNNN[1];
        $organization->description = 'yesss';
        if (!$organization->save()) {
            self::$errors .= "updateStudentsOrganizations: Inserting a new organization $DDBNNN[1] has failed \n";
        }
        $organization->uuid = $organization->id;
        if (!$organization->districts()->sync(array(1 => 10))) {
            self::$errors .= "updateStudentsOrganizations: Assigning organization $organization->id to ditrict has failed \n";
        }
    }

    protected static function updateStudentsGroups($data)
    {
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
            //Adding groups
            $organization_id = self::$organization_id;

        $group = Group::where('organization_id', '=', $organization_id);

        if (!$group->count()) {
            $group = new Group();
            $group->organization_id = $organization_id;
            $group->class_number = $data['off_cls'];
            if (!$group->save()) {
                self::$errors .= "updateStudentsGroups: Inserting a new organization $organization_id has failed \n";
            }
        } else {
            $group = $group->first();
            $group->organization_id = $organization_id;
            $group->class_number = $data['off_cls'];
            if (!$group->save()) {
                self::$errors .= "updateStudentsGroups: Updating a new organization $organization_id has failed \n";
            }
        }

        return $group->id;
    }

    protected static function updateStudentsData($data)
    {
        $DDBNNN = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $data['ddbnnn']);
            //Adding students
            $user = User::firstOrNew(['student_id' => $data['student_id']]);
        $username = $data['first_name'].'-'.$data['student_id'].'-'.$data['last_name'];
        $username = str_slug($username).'@changemyworldnow.com';
        $user->username = $username;
        $user->student_id = $data['student_id'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->gender = $data['sex'];
        $user->birthdate = $data['birth_dt'];
        if (!$user->save()) {
            self::$errors .= "updateStudentsData: Inserting a new student $username has failed \n";
        }
        $student_id = (!$user->id) ? $user->uuid : $user->id;

            //Add parents
            if ($data['adult_first_1'] && $data['adult_last_1']) {
                $username = $data['adult_first_1'].' '.$data['adult_last_1'];
                $username = str_slug($username).'@changemyworldnow.com';
                $parent_id = $data['adult_first_1'].' '.$data['adult_last_1'];
                $parent_id = str_slug($parent_id).'@changemyworldnow.com';
                $parent = User::firstOrNew(['student_id' => $parent_id, 'username' => $username]);
                $parent->username = $username;
                $parent->student_id = $parent_id;
                $parent->first_name = $data['adult_first_1'];
                $parent->last_name = $data['adult_last_1'];
                if (!$parent->save()) {
                    self::$errors .= "updateStudentsData: Inserting a new parent $username has failed \n";
                }
                $teacher_id = $parent_id = (!$parent->id) ? $parent->uuid : $parent->id;
            }
        $user->uuid = $student_id;

            /*$user->guardians()->sync(
                [$student_id => $parent_id]);*/

            //$user->guardianReference()->sync([$student_id]);

            if (!$user->guardianReference->contains($student_id) && $student_id) {
                $user->guardianReference()->sync(array(
                    $student_id => array(
                        'user_id' => $student_id,
                        'first_name' => $data['adult_first_1'],
                        'last_name' => $data['adult_last_1'],
                    ),
                ));
            }

        $allclasses = Group::where('class_number', $data['off_cls'])->lists('cluster_class', 'id')->toArray();
        $primary_class = $user->groups()->sync(
                array(
                    $student_id => array('roleable_id' => key($allclasses), 'role_id' => 3),
                )
            );

        if (!$primary_class) {
            self::$errors .= "updateStudentsData: Assigning teacher $teacher_id to cluster class has failed \n";
        }

        if ($allclasses) {
            foreach ($allclasses as $id => $classes) {
                $class = explode(',', $classes);
            }
        }

        $cluster_class = Group::whereIn('class_number', $class)->lists('id')->toArray();

        foreach ($cluster_class as $cls) {
            $sub_class = $user->groups()->attach(
                    array(
                        $student_id => array('roleable_id' => $cls, 'role_id' => 3),
                    )
                );

            if (!$sub_class == null) {
                self::$errors .= "updateStudentsData: Assigning student $student_id to cluster class has failed \n";
            }
        }
    }

    protected static function mailNotification($data)
    {
        //@TODO email notification has been temporarily disabled. JT 10/11
            return false;
        $notifier = new Notifier();
        $notifier->to = Auth::user()->email;
        $notifier->subject = 'Your import is completed at '.date('m-d-Y h:i:s A');
        $notifier->template = 'emails.import';
        $notifier->attachData(['user' => Auth::user()]);
        $notifier->send();
    }

    protected static function createReport()
    {
        $path = base_path('storage/app/error_log.csv');
        $write = \File::put($path, $this->errors);
    }
}
