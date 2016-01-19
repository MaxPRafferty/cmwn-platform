<?php

namespace app\Http\Controllers\Api;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use app\Image;

class ImagesController extends ApiController
{
    public function webhook()
    {
        $signature = Request::header('X-Cld-Signature');
        $timestamp = Request::header('X-Cld-Timestamp');
        $data = Request::getContent();
        $api_secret = env('CLOUDINARY_API_SECRET');

        if ($signature == sha1("$data{$timestamp}{$api_secret}")) {
            if (Input::get('notification_type') == 'moderation') {
                $image = Image::where('cloudinary_id', Input::get('public_id'))->first();

                if ($image) {
                    $moderation_status = Input::get('moderation_status');

                    switch ($moderation_status) {
                        case 'approved':
                            $image->moderation_status = 1;
                            break;

                        case 'rejected':
                            $image->moderation_status = -1;
                            break;

                        case 'pending':
                            $image->moderation_status = 0;
                            break;
                    }

                    $image->save();
                } else {
                    return $this->errorNotFound('Failed to Locate Image.');
                }
            } else {
                return $this->errorNotFound('Invalid Notification Type');
            }
        } else {
            return $this->errorUnauthorized('Not Authorized.');
        }
    }
}
