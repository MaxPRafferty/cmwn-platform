<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Cloudinary;

class Image extends Model
{
    protected $fillable = array('cloudinary_id');

    public static $imageUpdateRules = array(
        'cloudinary_id' => 'required|string',
    );

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getModerationState()
    {
        Cloudinary::config(array(
          'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
          'api_key' => env('CLOUDINARY_API_KEY'),
          'api_secret' => env('CLOUDINARY_API_SECRET'),
        ));

        $api = new Cloudinary\Api();
        $list = $api->resource($this->cloudinary_id);

        if(isset($list['moderation'])) {
            return $list['moderation'][0]['status'];
        } else {
            return 'no moderation';
        }

    }
}
