<?php

namespace Feed;

/**
 * Class UserFeed
 * @package Feed
 */
class UserFeed extends Feed implements UserFeedInterface
{
    /**
     * @var int $readFlag
     */
    protected $readFlag;

    /**
     * UserFeed constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        parent::__construct($array);
        $this->exchangeArray($array);
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array = [])
    {
        parent::exchangeArray($array);
        if (isset($array['read_flag'])) {
            $this->setReadFlag($array['read_flag']);
        }
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy() : array
    {
        $data = parent::getArrayCopy();
        $data['read_flag'] = $this->getReadFlag();
        return $data;
    }

    /**
     * @return int
     */
    public function getReadFlag()
    {
        return $this->readFlag;
    }

    /**
     * @param int $readFlag
     */
    public function setReadFlag($readFlag)
    {
        $this->readFlag = $readFlag;
    }
}
