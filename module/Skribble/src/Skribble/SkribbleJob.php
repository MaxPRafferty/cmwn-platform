<?php

namespace Skribble;

use Job\Aws\Sqs\SqsJobInterface;
use Job\Aws\Sqs\SqsJobTrait;

/**
 * Class SkribbleJob
 */
class SkribbleJob implements SqsJobInterface
{
    use SqsJobTrait;

    /**
     * @var SkribbleInterface
     */
    protected $skribble;

    /**
     * @var string
     */
    protected $siteUrl;

    /**
     * SkribbleJob constructor.
     *
     * @param SkribbleInterface $skribble
     * @param $siteUrl
     */
    public function __construct(SkribbleInterface $skribble, $siteUrl)
    {
        $this->skribble = $skribble;
        $this->siteUrl  = $siteUrl;
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy()
    {
        $skribbleBase = 'https:// ' . $this->siteUrl .
            '/user/' .
            $this->skribble->getCreatedBy() .
            '/skribble/' .
            $this->skribble->getSkribbleId();

        return [
            'skirbble_id'  => $this->skribble->getSkribbleId(),
            'skribble_url' => $skribbleBase,
            'post_back'    => $skribbleBase . '/complete',
        ];
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        // noop
    }

    /**
     * @inheritDoc
     */
    public function exchangeArray(array $data)
    {
        // noop
    }
}
