<?php

namespace app\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class ImagesController extends ApiController
{
    public function webhook()
    {
        //var_dump(Input::all(''));

        $signature = Request::header('X-Cld-Signature');
        $timestamp = Request::header('X-Cld-Timestamp');
        $data = Request::getContent();
        $api_secert = env('CLOUDINARY_API_KEY');

        if ($signature == sha1("{$data}{$timestamp}{$api_secert}")) {
            var_dump(Input::get('public_id'));
        } else {
            return $this->errorUnauthorized('Not authorized.');
        }
    }
}
