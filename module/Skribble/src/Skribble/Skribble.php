<?php

namespace Skribble;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Zend\Filter\StaticFilter;

/**
 * Class Skribble
 */
class Skribble implements SkribbleInterface
{
    use DateDeletedTrait;
    use DateCreatedTrait;
    use DateUpdatedTrait;

    /**
     * @var string
     */
    protected $skirbbleId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $friendTo;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $version = self::CURRENT_VERSION;

    /**
     * Skribble constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
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
        $defaults = [];
        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
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
        return [];
    }

    /**
     * @return string
     */
    public function getSkirbbleId()
    {
        return $this->skirbbleId;
    }

    /**
     * @param string $skirbbleId
     */
    public function setSkirbbleId($skirbbleId)
    {
        $this->skirbbleId = $skirbbleId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getFriendTo()
    {
        return $this->friendTo;
    }

    /**
     * @param string $friendTo
     */
    public function setFriendTo($friendTo)
    {
        $this->friendTo = $friendTo;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
