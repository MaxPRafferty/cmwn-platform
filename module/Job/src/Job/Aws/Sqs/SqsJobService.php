<?php

namespace Job\Aws\Sqs;

use Aws\Sqs\SqsClient;
use Job\JobInterface;
use Job\Service\JobServiceInterface;
use Zend\Json\Json;

/**
 * Class SqsJobService
 */
class SqsJobService implements JobServiceInterface
{
    /**
     * @var SqsClient
     */
    protected $sqsClient;

    /**
     * @var string
     */
    protected $queueUrl;

    /**
     * SqsJobService constructor.
     *
     * @param SqsClient $client
     * @param string $queueUrl
     */
    public function __construct(SqsClient $client, $queueUrl)
    {
        $this->sqsClient = $client;
        $this->queueUrl  = $queueUrl;
    }

    /**
     * @inheritDoc
     */
    public function sendJob(JobInterface $job)
    {
        $this->sqsClient->sendMessage([
            'QueueUrl'    => $this->queueUrl,
            'MessageBody' => Json::encode($job->getArrayCopy()),
        ]);
    }
}
