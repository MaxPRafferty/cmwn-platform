<?php

namespace Job\Aws\Sns;

use Aws\Sns\SnsClient;
use Job\JobInterface;
use Zend\Json\Json;

/**
 * Class SnsJobService
 */
class SnsJobService implements SnsServiceInterface
{
    /**
     * @var SnsClient
     */
    protected $sqsClient;

    /**
     * @var string
     */
    protected $snsArn;

    /**
     * SqsJobService constructor.
     *
     * @param SnsClient $client
     * @param string $snsArn
     */
    public function __construct(SnsClient $client, $snsArn)
    {
        $this->sqsClient = $client;
        $this->snsArn    = $snsArn;
    }

    /**
     * @inheritDoc
     */
    public function sendJob(JobInterface $job)
    {
        $this->sqsClient->publishAsync([
            'TargetArn'        => $this->snsArn,
            'MessageStructure' => 'json',
            'MessageBody'      => Json::encode($job->getArrayCopy()),
        ]);
    }
}
