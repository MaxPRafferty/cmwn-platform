<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;
use app\cmwn\Traits\RoleTrait;
use app\cmwn\Traits\EntityTrait;

class Group extends Model
{
    use RoleTrait;
    use EntityTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'groups';

    protected $fillable = array('organization_id', 'title');

    public static $updateRules = array(
        'title[]' => 'string',
    );

    public static $createRules = array(
        'title[]' => 'string',
    );

    public function organization()
    {
        return $this->belongsTo('app\Organization');
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
