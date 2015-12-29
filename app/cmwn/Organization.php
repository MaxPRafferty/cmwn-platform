<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use app\cmwn\Traits\RoleTrait;

class Organization extends Model
{
    use RoleTrait;

    protected $table = 'organizations';

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
