<?php

namespace app\cmwn\Services;

use app\Group;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\District;
use app\Organization;
use app\User;
use Illuminate\Support\Facades\Auth;
use Excel;

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
        $organization_id = $this->data['parms']['organization_id'];

        $file_path = base_path('storage/app/'.$this->data['file']);

        $this->excel = Excel::load($file_path, function ($reader) use ($organization_id) {

            $errors = [];

            $reader->each(function ($sheet) use ($organization_id, &$errors) {
                $errors = array_merge($errors, self::processSheet($sheet, $organization_id));
            });

            //var_dump($errors);

            //$this->mailNotification($errors);
        });
    }

    private static function processSheet($sheet, $organization_id)
    {
        switch ($sheet->getTitle()) {
            case 'Classes':
                // self::classes($sheet, $organization_id);
                return ['classes' => ['error' => 'test']];
                break;

            case 'Teachers':
                // self::teachers($sheet, $organization_id);
                return ['teachers' => ['error' => 'test']];
                break;

            case 'Students':
                return ['students' => self::students($sheet)];
                break;

            default:
                # code...
                break;
        }
    }

    private static function classes($sheet, $organization_id)
    {
        echo('Classes');

        $sheet->each(function ($row) use ($organization_id) {
            self::updateClass($row, $organization_id);
        });
    }

    private static function teachers($sheet, $organization_id)
    {
        echo('Teachers');
    }

    private static function students($sheet)
    {
        $errors = [];

        $sheet->each(function ($row) use (&$errors) {

            $result = self::parseDdbnn($row->ddbnnn, function ($ditrict_code, $school_code) use ($row) {

                //Districts
                return self::updateDistrict($ditrict_code, 1, function ($district_id) use ($school_code, $row) {

                    //Schools
                    return self::updateSchool($school_code, $district_id, function ($school_id) use ($row) {

                        //Students
                        return self::updateClass($row->off_cls, $school_id, function ($class_id) use ($row) {
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

    private static function parseDdbnn($ddbnnn, $callback)
    {
        $result = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $ddbnnn);

        if (isset($result[0]) && isset($result[1])) {
            return $callback($result[0], $result[1]);
        } else {
            return self::constructError('Cannot Parse DDBNN: "'.$ddbnnn.'"');
        }
    }

    protected static function updateStudent($row, $class_id)
    {
        if (isset($row->student_id) && !empty($row->student_id)) {
            $user = User::firstOrNew(['student_id' => $row->student_id]);

            $user->student_id = $row->student_id;
            $user->first_name = $row->first_name;
            $user->last_name = $row->last_name;
            $user->gender = $row->sex;
            $user->birthdate = $row->birth_dt;

            $user->save();

            if ($user->taking_classes->where('id', $class_id)->count() == 0) {
                $user->groups()->attach([$class_id => ['role_id' => 1]]);
            }

        } else {
            return self::constructError('Could not create student. Student ID not set!');
        }
    }

    protected static function updateDistrict($code, $system_id, $callback)
    {
        $district = District::firstOrNew(['code' => $code], ['system_id' => $system_id]);
        $district->code = $code;
        $district->system_id = $system_id;
        $district->title = $code;
        $district->system_id = $system_id;
        $district->save();

        return $callback($district->id);
    }

    protected static function updateSchool($school_code, $district_id, $callback)
    {
        $organization = Organization::where(['code' => $school_code])
                        ->whereHas('districts', function ($query) use ($district_id) {
                            $query->where('districts.id', $district_id);
                        })->first();

        if (!$organization) {
            $organization = new Organization();
            $organization->code = $school_code;
            $organization->save();

            $organization->districts()->sync([$district_id]);
        }

        return $callback($organization->id);
    }

    protected static function updateClass($class_code, $school_id, $callback)
    {
        $group = Group::firstOrNew(['code' => $class_code, 'organization_id' => $school_id]);
        $group->code = $class_code;
        $group->organization_id = $school_id;
        $group->save();

        return $callback($group->id);
    }

    protected static function constructError($message)
    {
        return ['error' => $message];
    }

    protected static function mailNotification($data)
    {
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
