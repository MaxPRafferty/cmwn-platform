<?php

namespace Job\Aws\Sqs;

/**
 * Trait SqsJobTrait
 */
trait SqsJobTrait
{
    /**
     * @var string
     */
    protected $sqsUrl;

    /**
     * Gets the Sqs Queue Url
     *
     * @return string
     */
    public function getQueueUrl()
    {
        return $this->sqsUrl;
    }

    /**
     * Sets the Sqs Queue Url
     *
     * @param $queueUrl
     */
    public function setQueueUrl($queueUrl)
    {
        $this->sqsUrl = $queueUrl;
    }
}
