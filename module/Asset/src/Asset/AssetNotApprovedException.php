<?php

namespace Asset;

use Exception;

/**
 * Class AssetNotApprovedException
 */
class AssetNotApprovedException extends \Exception
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * AssetNotApprovedException constructor.
     * @param Image $image
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(Image $image, $message = "", $code = 0, \Exception $previous = null)
    {
        $this->image = $image;
        $message     = 'Not approved';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
