<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use app\cmwn\Traits\RoleTrait;
use app\cmwn\Traits\EntityTrait;

class Organization extends Model
{
    use RoleTrait, EntityTrait;

    protected $table = 'organizations';

    protected $fillable = [
        'code',
    ];

    public static $createRules = [];
    public static $updateRules = [];

    public function groups()
    {
        return $this->hasMany('app\Group');
    }

    public function districts()
    {
        return $this->belongsToMany('app\District');
    }

    public function images()
    {
        return $this->morphMany('app\Image', 'imageable');
    }

    public function updateOrganization($parameters)
    {
        if (isset($parameters['title'])) {
            $this->title = $parameters['title'];
        }

        if (isset($parameters['description'])) {
            $this->description = $parameters['description'];
        }

        return $this->save();
    }
}
