<?php

namespace app\cmwn\Traits;

use Illuminate\Support\Facades\Auth;
use app\User;

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
}
