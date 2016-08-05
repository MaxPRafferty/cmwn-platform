<?php

namespace Skribble\Rule;

use Media\Media;
use Media\MediaInterface;

/**
 * Class AbstractRule
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * AbstractRule constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->exchangeArray($options);
    }

    /**
     * @inheritDoc
     */
    public function exchangeArray($array)
    {
        $this->setMedia(new Media($array));
        if ($this instanceof StateRuleInterface && isset($array['state'])) {
            $this->setState($array['state']);
        }
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy()
    {
        $return               = $this->getMedia()->getArrayCopy();

        // In case we do not have a media rule or empty media
        if (!isset($return['asset_type']) || empty($return['asset_type'])) {
            $return['asset_type'] = $this->getRuleType();
        }

        if ($this instanceof StateRuleInterface) {
            $return['state'] = $this->getState();
        }

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function setMedia(MediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @inheritDoc
     */
    public function getMedia()
    {
        return $this->media;
    }
}
