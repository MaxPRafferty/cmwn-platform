<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use app\cmwn\Traits\RoleTrait;

class Organization extends Model
{
    use RoleTrait;

    protected $table = 'organizations';

    /**
     * The primaryKey is set to id by default
     * Changed to uuid
     * @var string
     */
    //protected $primaryKey = 'uuid';

    protected $fillable = [
        'code',
    ];

    /**
     * [$organizationUpdateRules description].
     *
     * @var array
     */
    public static $organizationUpdateRules = array(
        'title[]' => 'string',
        //'role[]'=>'required',
        //'role[]'=>'required|regex:/^[0-9]?$/',
    );

    public function groups()
    {
        return $this->hasMany('app\Group');
    }

    public function districts()
    {
        return $this->belongsToMany('app\District');
    }

    public function updateParameters($parameters)
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
