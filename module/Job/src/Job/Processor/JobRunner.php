<?php

namespace Job\Processor;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * Class JobRunner
 */
class JobRunner implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * Contains a list of allowed jobs that can be executed
     *
     * This list comes from the config using the allowed_jobs key.
     * @var array allowed jobs to be run
     */
    protected $allowedJobs = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $phpPath = '/usr/bin/php';

    /**
     * JobRunner constructor.
     *
     * @param array $config
     * @param LoggerInterface $logger
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        if (array_key_exists('php_path', $config)) {
            $this->phpPath = $config['php_path'];
        }

        $this->allowedJobs = $config['allowed_jobs'];
        $this->setLogger($logger);
    }

    /**
     * @param $jobName
     * @param array $params
     */
    public function setJob($jobName, array $params = [])
    {
        if (!array_key_exists($jobName, $this->allowedJobs)) {
            throw new \RuntimeException(sprintf('Job with the name %s is not allowed to be executed', $jobName));
        }

        $this->getLogger()->debug('Building job command');
        $jobSpec  = $this->allowedJobs[$jobName];
        $command  = $jobSpec['command'];

        $this->getLogger()->debug('Command to run: ' . $command);
        $paramStr = '';
        foreach ($jobSpec['params'] as $jobParam) {
            $this->getLogger()->debug('Found param: ' . $jobParam);
            $valueFlag = lcfirst(StaticFilter::execute($jobParam, UnderscoreToCamelCase::class));

            $paramStr .= ' --' . $valueFlag . '=' . escapeshellarg($params[$jobParam]);
            $this->getLogger()->debug('Param String: ' . $jobParam);
        }

        $this->command = $command . $paramStr;
        $this->getLogger()->debug('Complete command: ' . $this->command);
    }

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    public function perform()
    {
        $fullCommand = $this->phpPath . ' ' . APPLICATION_PATH . '/public/index.php ' . $this->command;
        $this->getLogger()->notice('Executing: ' . $fullCommand);
        system($fullCommand, $exitCode);
        return $exitCode;
    }
}
