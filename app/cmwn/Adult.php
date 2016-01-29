<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adult extends Model
{
    public function organization()
    {
        return $this->belongsTo('app\User');
    }
}
