<?php

namespace Job\Aws\Sqs;

use Job\JobInterface;

/**
 * Class SqsJobInterface
 */
interface SqsJobInterface extends JobInterface
{
    /**
     * Gets the Sqs Queue Url
     *
     * @return string
     */
    public function getQueueUrl();

    /**
     * Sets the Sqs Queue Url
     *
     * @param $queueUrl
     */
    public function setQueueUrl($queueUrl);
}
