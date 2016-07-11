<?php

namespace Job\Aws\Sqs;

use Aws\Sqs\SqsClient;
use Job\JobInterface;
use Job\RuntimeException;
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
     * SqsJobService constructor.
     *
     * @param SqsClient $client
     */
    public function __construct(SqsClient $client)
    {
        $this->sqsClient = $client;
    }

    /**
     * @inheritDoc
     */
    public function sendJob(JobInterface $job)
    {
        if (!$job instanceof SqsJobInterface) {
            throw new RuntimeException('JobInterface passed to SqsJobService, expected SqsInterface');
        }

        $this->sqsClient->sendMessage([
            'QueueUrl'    => $job->getQueueUrl(),
            'MessageBody' => Json::encode($job->getArrayCopy()),
        ]);
    }
}
