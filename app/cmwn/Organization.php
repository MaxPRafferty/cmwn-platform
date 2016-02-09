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

    public static $createRules = ['code' => 'required', 'district_id' => 'required'];
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

    protected static function getOrganizationWithDistric($organization_code, $district_id)
    {
        return Organization::where(['code' => $organization_code])
                        ->whereHas('districts', function ($query) use ($district_id) {
                            $query->where('districts.id', $district_id);
                        })->first();
    }

    public function updateOrganization($parameters)
    {

        if (isset($parameters['code'])) {
            $this->code = $parameters['code'];
        }

        if (isset($parameters['title'])) {
            $this->title = $parameters['title'];
        }

        if (isset($parameters['description'])) {
            $this->description = $parameters['description'];
        }

        $this->save();

        if (isset($parameters['district_id'])) {
            $this->districts()->sync([$parameters['district_id']]);
        }
    }
}
