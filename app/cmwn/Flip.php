<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use app\cmwn\Traits\RoleTrait;

class Flip extends Model
{
    use RoleTrait;
    use SoftDeletes;
    protected $table = 'flips';

    public static $flipUpdateRules = array(
        'title' => 'required | string',
        //'role[]'=>'required',
        //'role[]'=>'required|regex:/^[0-9]?$/',
    );

    public function games()
    {
        return $this->belongsToMany('app\Flip', 'game_flips', 'game_id', 'flip_id');
    }

    public function updateParameters($parameters)
    {
        if (isset($parameters['user_id'])) {
            $this->user_id = $parameters['user_id'];
        }

        if (isset($parameters['message'])) {
            $this->message = $parameters['message'];
        }

        if (isset($parameters['story'])) {
            $this->story = $parameters['story'];
        }

        return $this->save();
    }

}
