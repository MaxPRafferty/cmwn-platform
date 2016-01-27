<?php

namespace app\Http\Controllers\Api;

use app\Transformer\MasterTransformer;
use app\Repositories\SideBarItems;
use Request;
use app\AdminTool;
use app\Jobs\ImportCSV;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;

class MasterController extends ApiController
{

    use DispatchesJobs;

    public function sidebar()
    {
        $userspecific = new SideBarItems();
        $sidebar = $userspecific->getAll();
        $sidebar = array('tags' => $sidebar);

        return $this->respondWithCollection($sidebar, new MasterTransformer());
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make(\Input::all(), AdminTool::$uploadCsvRules);

        $file = Request::file('yourcsv');

        //the files are stored in storage/app/*files*
        $user_id = Auth::user()->id;
        $file_name = $file->getFilename().'_userid'.$user_id.'_time'.time();
        $extension = $file->getClientOriginalExtension();
        $full_file_name = $file_name.'.'.$extension;
        $output = Storage::disk('local')->put($file_name.'.'.$extension, \File::get($file));

        $data = ['file' => $full_file_name, 'currentUser' => $this->currentUser];

        $job = (new ImportCSV($data))->delay(60)->onQueue('emails');

        $this->dispatch($job);

        //return $this->respondWithArray($output);
    }
}
