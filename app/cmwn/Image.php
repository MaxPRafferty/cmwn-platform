<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Image extends Model
{
    // protected $table = 'images';

    // protected $fillable = array('imageable_id', 'imageable_type');

    public static $imageUpdateRules = array(
        'cloudinary_id' => 'required|string',
    );

    public function imageable()
    {
        return $this->morphTo();
    }

}
