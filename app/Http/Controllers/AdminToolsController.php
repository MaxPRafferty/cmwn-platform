<?php

namespace app\Http\Controllers;

use app\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Request;
use app\AdminTool;
use app\Jobs\ImportCSV;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class AdminToolsController extends ApiController
{
    public function uploadCsv(Request $request)
    {
        if (Request::isMethod('post')) {
            $validator = Validator::make(Input::all(), AdminTool::$uploadCsvRules);

            if ($validator->passes()) {
                $file = \Request::file('yourcsv');
                //the files are stored in storage/app/*files*
                $output = Storage::put('yourcsvfile.csv', file_get_contents($file));
                if ($output) {
                    $importType = \Request::get('importType');
                    $data = array(
                        'parms' => array(),
                    );
                    $this->dispatch(new ImportCSV($data));

                    return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors('Your file has been successfully uploaded. You will receive an email notification once the import is completed.');
                } else {
                    return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors('Something went wrong with your upload. Please try again.');
                }
            } else {
                return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
            }
        }

        return view('admin/uploadcsv');
    }
}
