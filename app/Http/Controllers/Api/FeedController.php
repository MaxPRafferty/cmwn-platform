<?php

namespace app\Http\Controllers\Api;

use Input;
use app\Post;
use app\Transformer\PostTransformer;

class FeedController extends ApiController
{
    public function index()
    {

        $posts = Post::all();

        return $this->respondWithCollection($posts, new PostTransformer());
    }
}
