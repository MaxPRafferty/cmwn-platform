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

        $list = $api->resources_by_ids([$this->cloudinary_id]);

        foreach ($list['resources'] as $item) {
            if($item['public_id'] == $this->cloudinary_id) {
                $image = $api->resource($this->cloudinary_id);
                $this->url = $item['url'];
                break;
            }
        }

        if(isset($image['moderation'])) {
            return $image['moderation'][0]['status'];
        } else {
            return 'no moderation';
        }

    }
}
