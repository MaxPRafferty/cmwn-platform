<?php

namespace app\Transformer;

use app\Image;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */
    public function transform(Image $image)
    {
        if ($image->moderation_state == 1) {
            return [
                'url' => 'https://www.changemyworldnow.com/ff50fa329edc8a1d64add63c839fe541.png',
            ];
        } else {
            return [
                'url' => $image->url,
            ];
        }
    }
}
