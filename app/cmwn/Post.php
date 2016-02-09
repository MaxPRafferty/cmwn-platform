<?php

namespace app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Post extends Model
{
    use SoftDeletes;

    public function images()
    {
        return $this->morphOne('images');
    }

    public function updatePost($parameters)
    {
        if (isset($parameters['title'])) {
            $this->title = $parameters['title'];
        }

        if (isset($parameters['description'])) {
            $this->description = $parameters['description'];
        }

        if (isset($parameters['story'])) {
            $this->story = $parameters['story'];
        }

        return $this->save();
    }

}
