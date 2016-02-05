<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use app\cmwn\Traits\RoleTrait;
use app\cmwn\Traits\EntityTrait;

class District extends Model
{
    use RoleTrait, EntityTrait, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'districts';

    protected $fillable = [
        'code',
    ];

    public static $updateRules = array(
        'title' => 'string',
    );

    public static $createRules = array(
        'system_id' => 'required|integer',
        'code' => 'required',
    );

    public function organizations()
    {
        return $this->belongsToMany('app\Organization');
    }

    public function groups()
    {
        $organization = $this->organizations->lists('id')->toArray();

        return Group::whereIn('organization_id', $organization)->get();
    }

    public function images()
    {
        return $this->morphMany('app\Image', 'imageable');
    }

    public function updateDistrict($params)
    {
        if (isset($params['system_id'])) {
            $this->system_id = $params['system_id'];
        }

        if (isset($params['code'])) {
            $this->code = $params['code'];
        }

        if (isset($params['title'])) {
            $this->title = $params['title'];
        }

        if (isset($params['middle_name'])) {
            $this->middle_name = $params['middle_name'];
        }

        if (isset($params['description'])) {
            $this->last_name = $params['description'];
        }

        if ($this->save()) {
            return true;
        }

        return false;
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName($query, $val)
    {
        return $query->where('title', $val);
    }
}
