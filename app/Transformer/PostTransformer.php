<?php

namespace app\Transformer;

use app\Flip;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */
    public function transform(Post $post)
    {
        return [
            'uuid' => $post->uuid,
            'message' => $post->title,
            'story' => $post->story,
            'image' => $post->description,
            'created_at' => (string) $post->created_at,
        ];
    }

    /**
     * Embed User.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeUsers(Flip $flip)
    {
        $users = $flip->users;

        return $this->collection($users, new UserTransformer());
    }
}
