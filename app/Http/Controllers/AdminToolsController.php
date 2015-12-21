<?php

namespace app\Http\Controllers;
use app\District;
use app\Http\Controllers\Api\ApiController;
use app\Organization;
use Illuminate\Support\Facades\Request;
use app\AdminTool;
use app\Jobs\ImportCSV;
use app\User;
use Illuminate\Support\Facades\Hash;
use app\Http\Requests;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

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
                if($output){
                    $importType = \Request::get('importType');
                    $data = array(
                        'parms' => array()
                    );
                    $this->dispatch(new ImportCSV($data));
                    return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors
                    ('Your file has been successfully uploaded. You will receive an email notification once the import is completed.');
                } else {
                    return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors
                    ('Something went wrong with your upload. Please try again.');
                }
            }else{
                return Redirect::to('admin/uploadcsv')->with('message', 'The following errors occurred')->withErrors
                ($validator)->withInput();
            }

        }
        return view('admin/uploadcsv');
    }


    public function importfiles(Request $request){
        if (Request::isMethod('post')) {
            if (!Auth::check()){
                return $this->errorUnauthorized('Sorry you must be logged on.');
            }
            $validator = Validator::make(Input::all(), AdminTool::$uploadCsvRules);
            if ($validator->passes()) {
                $file = \Request::file('yourcsv');
                $organization_id = \Request::get('organizations');

                if ($file==''){
                    return $this->errorInternalError('The import has failed: Please upload your csv file. ');
                }

                //the files are stored in storage/app/*files*
                $user_id = Auth::user()->id;

                $file_name = $file->getFilename()."_userid".$user_id."_time".time();
                $extension = $file->getClientOriginalExtension();
                $full_file_name = $file_name.".".$extension;
                $output = Storage::disk('local')->put($file_name.'.'.$extension,  \File::get($file));

                if($output){
                    $data = array(
                        'file' =>$full_file_name,
                        'parms' => array('organization_id' => $organization_id)
                    );
                    $error = $this->dispatch(new ImportCSV($data));
                    $error_log = 'storage.app.error_log.csv';
                    if (!$error) {
                        return $this->respondWithArray(array(
                            'message' => 'Your file has been successfully uploaded.You will receive an email notification once the import is completed',
                            'error_log' => $error_log
                        ));
                    }
                    return $this->errorInternalError('The import has failed. Please see the error log:.' . $error_log);
                } else {
                    return $this->errorInternalError('The import has failed. Please try again.');
                }
            }else{
                $messages = print_r($validator->errors()->getMessages(), true);
                return $this->errorInternalError('The import has failed. ' . $messages);
            }

        }

    }
}
