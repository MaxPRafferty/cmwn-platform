<?php

namespace app\cmwn\Traits;

use Illuminate\Support\Facades\Auth;
use app\User;
use app\Image;

trait EntityTrait
{
    public static function findByUuid($uuid)
    {
        if ($uuid ==  'me' && get_class() == 'app\User') {
            return Auth::user();
        } else {
            return self::where('uuid', $uuid)->firstOrFail();
        }
    }

    public function updateImage($params)
    {
        $image = new Image();

        if (isset($params['url'])) {
            $image->url = $params['url'];
        }

        if (isset($params['cloudinary_id'])) {
            $image->cloudinary_id = $params['cloudinary_id'];
        }

        if ($this->images()->save($image)) {
            return true;
        }

        return false;
    }
}
