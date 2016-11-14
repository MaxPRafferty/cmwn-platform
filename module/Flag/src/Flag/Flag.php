<?php

namespace Flag;

use User\UserHydrator;
use User\UserInterface;
use Zend\Filter\StaticFilter;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Flag
 * @package Flag
 */
class Flag implements FlagInterface, ArraySerializableInterface
{
    /**
     * @var string
     */
    protected $flagId;

    /**
     * @var UserInterface
     */
    protected $flagger;

    /**
     * @var UserInterface
     */
    protected $flaggee;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var UserHydrator
     */
    protected $hydrator;

    /**
     * Flag constructor.
     * @param array $array
     */
    public function __construct($array = [])
    {
        $this->hydrator = new UserHydrator();
        $this->exchangeArray($array);
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'flagger' => null,
            'flaggee' => null,
            'flag_id' => null,
            'url'     => null,
            'reason'  => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        $array = [];
        $array['flag_id'] = $this->getFlagId();
        $array['flagger'] = $this->getFlagger() instanceof UserInterface
            ? $this->getFlagger()->getArrayCopy()
            :$this->getFlagger();
        $array['flaggee'] = $this->getFlaggee() instanceof UserInterface
            ? $this->getFlaggee()->getArrayCopy()
            :$this->getFlaggee();
        $array['url']     = $this->getUrl();
        $array['reason']  = $this->getReason();

        return $array;
    }

    /**
     * @return string|null
     */
    public function getFlagId()
    {
        return $this->flagId;
    }

    /**
     * @param string $flagId
     */
    public function setFlagId($flagId)
    {
        $this->flagId = (string) $flagId;
    }

    /**
     * @inheritdoc
     */
    public function getFlagger()
    {
        return $this->flagger;
    }

    /**
     * @inheritdoc
     */
    public function setFlagger($flagger)
    {
        if (is_array($flagger)) {
            $this->flagger = $this->hydrator->hydrate($flagger, $this->flagger);
        }
        if ($flagger instanceof UserInterface) {
            $this->flagger = $flagger;
        }
    }

    /**
     * @inheritdoc
     */
    public function getFlaggee()
    {
        return $this->flaggee;
    }

    /**
     * @inheritdoc
     */
    public function setFlaggee($flaggee)
    {
        if (is_array($flaggee)) {
            $this->flaggee = $this->hydrator->hydrate($flaggee, $this->flaggee);
        }
        if ($flaggee instanceof UserInterface) {
            $this->flaggee = $flaggee;
        }
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @inheritdoc
     */
    public function getReason()
    {
        return $this->reason;
    }
}
