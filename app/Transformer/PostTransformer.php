<?php

namespace app\Transformer;

use app\Post;
use app\Image;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user',
        'image',
    ];

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
            'created_at' => (string) $post->created_at,
        ];
    }

    /**
     * Embed Image.
     *
     * @return League\Fractal\Resource\Collection
     */
    public function includeImage(Post $post)
    {
        $image = $post->image;

        return $this->item($image, new ImageTransformer());
    }
}
