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

    /**
     * [$updateRules description].
     *
     * @var array
     */
    public static $updateRules = array(
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
