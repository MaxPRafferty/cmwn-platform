<?php

namespace app\Http\Controllers\Api;

use Input;
use app\Transformer\FeedTransformer;

class FeedController extends ApiController
{
    public function index()
    {

        $post = Posts::all();

        return $this->respondWithCollection($posts, new PostTransformer());
    }
}
