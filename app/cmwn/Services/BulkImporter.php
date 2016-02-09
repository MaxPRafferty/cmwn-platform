<?php

namespace app\cmwn\Services;

use app\Group;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\District;
use app\Organization;
use app\User;
use app\Adult;
use Excel;
use Illuminate\Support\Facades\Storage;

class BulkImporter
{
    use DispatchesJobs;

    public function migratecsv()
    {
        $currentUser = $this->data['currentUser'];

        $file_path = base_path('storage/app/'.$this->data['file']);

        Excel::selectSheets('Classes', 'Teachers', 'Students')->load($file_path, function ($reader) use ($currentUser) {

            $errors = [];

            $reader->each(function ($sheet) use ($currentUser, &$errors) {
                $errors = array_merge($errors, self::processSheet($sheet, $currentUser));
            });

            $this->mailNotification($errors, $currentUser);

        });

        Storage::disk('local')->delete($this->data['file']);
    }

    private static function processSheet($sheet, $currentUser)
    {
        switch ($sheet->getTitle()) {
            case 'Classes':
                return ['classes' => self::classes($sheet, $currentUser)];
                break;

            case 'Teachers':
                return ['teachers' => self::teachers($sheet, $currentUser)];
                break;

            case 'Students':
                return ['students' => self::students($sheet, $currentUser)];
                break;

            default:
                # code...
                break;
        }
    }

    private static function classes($sheet, $currentUser)
    {
        $errors = [];

        $sheet->each(function ($row) use ($currentUser, &$errors) {

            $result = self::parseDdbnn($currentUser, $row->ddbnnn, function ($ditrict_code, $organization_code) use ($row) {

                //Districts
                return self::updateDistrict($ditrict_code, 1, function ($district_id) use ($organization_code, $row) {

                    //Schools
                    return self::updateSchool($organization_code, $district_id, function ($school_id) use ($row) {

                        //Classes
                        return self::updateClass($row, $school_id);
                    });
                });
            });

            if (isset($result['error'])) {
                $errors[] = $result['error'];
            }

        });

        return $errors;
    }

    private static function teachers($sheet, $currentUser)
    {
        $errors = [];

        $sheet->each(function ($row) use ($currentUser, &$errors) {

            $result = self::parseDdbnn($currentUser, $row->ddbnnn, function ($ditrict_code, $organization_code) use ($row) {

                //Districts
                return self::updateDistrict($ditrict_code, 1, function ($district_id) use ($organization_code, $row) {

                    //Schools
                    return self::updateSchool($organization_code, $district_id, function ($school_id) use ($row) {

                        switch ($row->type) {
                            case 'Principal':
                            case 'Assistant Principal':
                                self::updatePrincipal($row, $school_id);
                                break;

                            case 'Teacher':
                                return self::getClass($row->off_cls, $school_id, function ($class_id) use ($row) {
                                    self::updateTeacher($row, $class_id);
                                });
                                break;

                            default:
                                return self::constructError('The type "'.$row->type.'" is not recognized.');
                                break;
                        }
                    });
                });
            });

            if (isset($result['error'])) {
                $errors[] = $result['error'];
            }

        });

        return $errors;
    }

    private static function students($sheet, $currentUser)
    {
        $errors = [];

        $sheet->each(function ($row) use ($currentUser, &$errors) {

            $result = self::parseDdbnn($currentUser, $row->ddbnnn, function ($ditrict_code, $organization_code) use ($row) {

                //Districts
                return self::updateDistrict($ditrict_code, 1, function ($district_id) use ($organization_code, $row) {

                    //Schools
                    return self::updateSchool($organization_code, $district_id, function ($school_id) use ($row) {

                        //Classes
                        return self::getClass($row->off_cls, $school_id, function ($class_id) use ($row) {
                            return self::updateStudent($row, $class_id);
                        });
                    });
                });
            });

            if (isset($result['error'])) {
                $errors[] = $result['error'];
            }

        });

        return $errors;
    }

    private static function parseDdbnn($currentUser, $ddbnnn, $callback)
    {
        $result = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $ddbnnn);

        if (isset($result[0]) && isset($result[1])) {
            $district_code = $result[0];
            $organization_code = $result[1];

            $district = self::getDistrict($district_code, 1);

            if ($district) {
                $organization = self::getOrganizationWithDistric($organization_code, $district->id);
            } else {
                return self::constructError('The there is no district associated with "'.$ddbnnn.'"');
            }

            if ($organization) {
                if ($organization->canUpdate($currentUser)) {
                    return $callback($district_code, $organization_code);
                } else {
                    return self::constructError('The current user does not have permission to update "'.$ddbnnn.'"');
                }
            } else {
                return self::constructError('The there is no organization with the code "'.$organization_code.'" in the district with code "'.$district_code.'"');
            }
        } else {
            return self::constructError('Cannot Parse DDBNN: "'.$ddbnnn.'"');
        }
    }

    protected static function updateStudent($row, $class_id)
    {
        if (!empty($row->student_id)) {
            $user = User::firstOrNew(['student_id' => $row->student_id]);

            self::updateUser($user, $row);

            if ($user->takingClasses->where('id', $class_id)->count() == 0) {
                $user->groups()->attach([$class_id => ['role_id' => 1]]);
            }

            self::addAdult($user, $row);
        } else {
            return self::constructError('Could not create student. Student ID not set!');
        }
    }

    protected static function addAdult($user, $row)
    {
        $user->adults()->delete();

        if (!empty($row->adult_first_1) || !empty($row->adult_last_1)) {
            $adult_1 = new Adult();
            $adult_1->first_name = $row->adult_first_1;
            $adult_1->last_name = $row->adult_last_1;
            $adult_1->phone = $row->adult_phone_1;
            $adult_1->save();
            $adults[] = $adult_1;
        }

        if (!empty($row->adult_first_2) || !empty($row->adult_last_2)) {
            $adult_2 = new Adult();
            $adult_2->first_name = $row->adult_first_2;
            $adult_2->last_name = $row->adult_last_2;
            $adult_2->phone = $row->adult_phone_2;
            $adult_2->save();
            $adults[] = $adult_2;
        }

        if (!empty($row->adult_first_3) || !empty($row->adult_last_3)) {
            $adult_3 = new Adult();
            $adult_3->first_name = $row->adult_first_3;
            $adult_3->last_name = $row->adult_last_3;
            $adult_3->phone = $row->adult_phone_3;
            $adult_3->save();
            $adults[] = $adult_3;
        }

        if (isset($adults)) {
            $user->adults()->saveMany($adults);
        }
    }

    protected static function updateTeacher($row, $class_id)
    {
        if (!empty($row->email)) {
            $user = User::firstOrNew(['email' => $row->email]);

            self::updateUser($user, $row);

            if ($user->teachingClasses->where('id', $class_id)->count() == 0) {
                $user->groups()->attach([$class_id => ['role_id' => 2]]);
            }
        } else {
            return self::constructError('Could not create teacher. Their email was not set!');
        }
    }

    protected static function updatePrincipal($row, $school_id)
    {
        if (!empty($row->email)) {
            $user = User::firstOrNew(['email' => $row->email]);

            self::updateUser($user, $row);

            if ($user->organizations()->where('roleable_id', $school_id)->where('roleables.role_id', 2)->count() == 0) {
                $user->organizations()->attach([$school_id => ['role_id' => 2]]);
            }
        } else {
            return self::constructError('Could not create principal. Their email was not set!');
        }
    }

    protected static function updateUser(User $user, $row)
    {
        if (!empty($row->student_id)) {
            $user->student_id = $row->student_id;
        }

        if (!empty($row->email)) {
            $user->email = $row->email;
        }

        if (!empty($row->first_name)) {
            $user->first_name = $row->first_name;
        }

        if (!empty($row->last_name)) {
            $user->last_name = $row->last_name;
        }

        if (!empty($row->sex)) {
            $user->gender = $row->sex;
        }

        if (!empty($row->birth_dt)) {
            $user->birthdate = $row->birth_dt;
        }

        $user->save();

        return $user;
    }

    protected static function updateDistrict($code, $system_id, $callback)
    {
        $district = self::getDistrict($code, $system_id);
        $district->code = $code;
        $district->system_id = $system_id;
        $district->title = $code;
        $district->system_id = $system_id;
        $district->save();

        return $callback($district->id);
    }

    protected static function getDistrict($code, $system_id)
    {
        return District::firstOrNew(['code' => $code], ['system_id' => $system_id]);
    }

    protected static function updateSchool($organization_code, $district_id, $callback)
    {
        $organization = Organization::getOrganizationWithDistric($organization_code, $district_id);

        if (!$organization) {
            $organization = new Organization();
            $organization->code = $organization_code;
            $organization->save();

            $organization->districts()->sync([$district_id]);
        }

        return $callback($organization->id);
    }

    protected static function updateClass($row, $school_id, $callback = null)
    {
        $group = Group::firstOrNew(['code' => $row->off_cls, 'organization_id' => $school_id]);

        if (isset($row->title)) {
            $group->title = $row->title;
        }

        $group->code = $row->off_cls;
        $group->organization_id = $school_id;
        $group->save();

        if (isset($callback)) {
            return $callback($group->id);
        }
    }

    protected static function getClass($class_code, $school_id, $callback = null)
    {
        $group = Group::where(['code' => $class_code, 'organization_id' => $school_id])->first();

        if ($group) {
            if (isset($callback)) {
                return $callback($group->id);
            }
        } else {
            return self::constructError('Could not locate class with code: "'.$class_code.'" in a school with the following id: "'.$school_id.'"');
        }
    }

    protected static function constructError($message)
    {
        return ['error' => $message];
    }

    protected static function mailNotification($errors, $currentUser)
    {
        $notifier = new Notifier();
        $notifier->to = $currentUser->email;
        $notifier->subject = 'Import Report :: '.date('F jS, Y h:i:s A');
        $notifier->template = 'emails.import';
        $notifier->attachData(['errors' => $errors, 'user' => $currentUser]);
        $notifier->send();
    }

    protected static function createReport()
    {
        $path = base_path('storage/app/error_log.csv');
        $write = \File::put($path, $this->errors);
    }
}
