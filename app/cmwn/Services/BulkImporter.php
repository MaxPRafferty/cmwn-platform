<?php

namespace app\cmwn\Services;

use app\Group;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\District;
use app\Organization;
use app\User;
use Illuminate\Support\Facades\Auth;
use Excel;
use Exception;

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

            $reader->each(function ($sheet) use ($organization_id) {
                self::processSheet($sheet, $organization_id);
            });

        });
    }

    private static function processSheet($sheet, $organization_id)
    {
        switch ($sheet->getTitle()) {
            case 'Classes':
                // self::classes($sheet, $organization_id);
                break;

            case 'Teachers':
                // self::teachers($sheet, $organization_id);
                break;

            case 'Students':
                self::students($sheet);
                break;

            default:
                # code...
                break;
        }

        // // Loop through all rows
        // $sheet->each(function ($row) {
        //     var_dump(expression);
        // });
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

        // $sheet->each(function ($row) {
        //     var_dump($row);
        // });
    }

    private static function students($sheet)
    {
        $sheet->each(function ($row) {

            self::parseDdbnn($row->ddbnnn, function ($ditrict_code, $school_code) use ($row) {

                $district_id = self::updateDistrict($ditrict_code, 1);

                $school_id = self::updateSchool($school_code, $district_id);

                $class_id = self::updateClass($row->off_cls, $school_id);

            });
        });
    }

    private static function parseDdbnn($ddbnnn, $callback)
    {
        $result = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $ddbnnn);

        if (isset($result[0]) && isset($result[1])) {
            $callback($result[0], $result[1]);
        } else {
            throw new Exception('Cannot Parse DDBNN: "'.$ddbnnn.'"', 1);
        }
    }

    protected static function updateDistrict($code, $system_id)
    {
        $district = District::firstOrNew(['code' => $code], ['system_id' => $system_id]);
        $district->title = $code;
        $district->system_id = $system_id;
        $district->save();

        return $district->id;
    }

    protected static function updateSchool($school_code, $district_id)
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

        return $organization->id;
    }

    protected static function updateClass($class_code, $school_id)
    {
        $group = Group::firstOrNew(['code' => $class_code, 'organization_id' => $school_id]);
        $group->code = $class_code;
        $group->organization_id = $school_id;
        $group->save();

        return $group->id;
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
