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
     * SkribbleJob constructor.
     *
     * @param SkribbleInterface $skribble
     */
    public function __construct(SkribbleInterface $skribble)
    {
        $this->skribble = $skribble;
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy()
    {
        $skribbleBase  = 'user/' . $this->skribble->getCreatedBy() . '/skribble/' . $this->skribble->getSkribbleId();
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
