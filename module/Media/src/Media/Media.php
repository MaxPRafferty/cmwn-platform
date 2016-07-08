<?php

namespace Media;

use Zend\Filter\StaticFilter;

/**
 * Class Media
 */
class Media implements MediaInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $assetType;

    /**
     * @var string
     */
    protected $mediaId;

    /**
     * @var string
     */
    protected $src;

    /**
     * @var string
     */
    protected $checkType;

    /**
     * @var string
     */
    protected $checkValue;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var MediaProperties
     */
    protected $properties;

    /**
     * Media constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->properties = new MediaProperties();
        $this->exchangeArray($options);
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *2
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'media_id'    => null,
            'asset_type'  => null,
            'check_type'  => null,
            'check_value' => null,
            'mime_type'   => null,
            'src'         => null,
            'name'        => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            if ($this->properties->isProperty($key)) {
                $this->properties->setProperty($key, $value);
                continue;
            }

            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return array_merge(
            $this->properties->getArrayCopy(),
            [
                'media_id'   => $this->getMediaId(),
                'asset_type' => $this->getAssetType(),
                'check'      => [
                    'type'  => $this->getCheckType(),
                    'value' => $this->getCheckValue(),
                ],
                'mime_type'  => $this->getMimeType(),
                'src'        => $this->getSrc(),
                'name'       => $this->getName(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param array $check
     * @return MediaInterface
     */
    public function setCheck(array $check)
    {
        $checkDefaults = [
            'type'  => null,
            'value' => null,
        ];

        $check = array_merge($checkDefaults, $check);
        $this->setCheckType($check['type']);
        $this->setCheckValue($check['value']);
        return $this;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    /**
     * @param string $type
     *
     * @return MediaInterface
     */
    public function setAssetType($type)
    {
        $this->assetType = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * @param string $mediaId
     *
     * @return MediaInterface
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param string $url
     *
     * @return MediaInterface
     */
    public function setSrc($url)
    {
        $this->src = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckType()
    {
        return $this->checkType;
    }

    /**
     * @param string $checkType
     *
     * @return MediaInterface
     */
    public function setCheckType($checkType)
    {
        $this->checkType = $checkType;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckValue()
    {
        return $this->checkValue;
    }

    /**
     * @param string $checkValue
     *
     * @return MediaInterface
     */
    public function setCheckValue($checkValue)
    {
        $this->checkValue = $checkValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return MediaInterface
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }
}
