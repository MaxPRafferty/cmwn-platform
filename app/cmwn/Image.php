<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

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
}
