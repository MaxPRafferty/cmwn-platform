<?php

namespace Application\Log\Rollbar;

use Zend\Stdlib\AbstractOptions;

/**
 * Class Options
 */
class Options extends AbstractOptions
{
    const API_URL = 'https://api.rollbar.com/api/1/';

    /**
     * @var bool Enabled module or not
     */
    protected $enabled = false;

    /**
     * @var string project server-side access token
     */
    protected $accessToken = '';

    /**
     * @var string project client-side access token
     */
    protected $clientAccessToken = '';

    /**
     * @var string The base api url to post to (default 'https://api.rollbar.com/api/1/')
     */
    protected $baseApiUrl = self::API_URL;

    /**
     * @var int Flush batch early if it reaches this size. default: 50
     */
    protected $batchSize = 50;

    /**
     * @var bool True to batch all reports from a single request together. default true.
     */
    protected $batched = true;

    /**
     * @var string Name of the current branch (default 'master')
     */
    protected $branch = 'master';

    /**
     * @var bool record full stacktraces for PHP errors. default: true
     */
    protected $captureErrorBacktraces = true;

    /**
     * @var string Environment name, e.g. 'production' or 'development'
     */
    protected $environment = '';

    /**
     * @var array Associative array mapping error numbers to sample rates
     */
    protected $errorSampleRates = array();

    /**
     * @var string Either "blocking" (default) or "agent". "blocking" uses curl to send
     *             requests immediately; "agent" writes a relay log to be consumed by rollbar-agent.
     */
    protected $handler = "blocking";

    /**
     * @var string Path to the directory where agent relay log files should be written
     */
    protected $agentLogLocation = '/var/www';

    /**
     * @var string Server hostname. Default: null, which will result in a call to `gethostname()`)
     */
    protected $host;

    /**
     * @var \iRollbarLogger An object that has a log($level, $message) method
     */
    protected $logger;

    /**
     * @var int Max PHP error number to report. e.g. 1024 will ignore all errors
     *          above E_USER_NOTICE. default: 1024 (ignore E_STRICT and above)
     */
    protected $maxErrno = 1024;

    /**
     * @var array An associative array containing data about the currently-logged in user.
     *            Required: 'id', optional: 'username', 'email'. All values are strings.
     * @todo Replace array by object
     */
    protected $person = array();

    /**
     * @vara callable Function reference (string, etc. - anything that
     *                [call_user_func()](http://php.net/call_user_func) can handle) returning
     *                an array like the one for 'person'
     */
    protected $personFn;

    /**
     * @var string Path to your project's root dir
     */
    protected $root;

    /**
     * @var array Array of field names to scrub out of POST
     *
     * Values will be replaced with astrickses. If overridiing, make sure to list all fields you want to scrub,
     * not just fields you want to add to the default. Param names are converted
     * to lowercase before comparing against the scrub list.
     * default: ('passwd', 'password', 'secret', 'confirm_password', 'password_confirmation')
     */
    protected $scrubFields = array('passwd', 'password', 'secret', 'confirm_password', 'password_confirmation');

    /**
     * @var bool Whether to shift function names in stack traces down one frame, so that the
     *           function name correctly reflects the context of each frame. default: true.
     */
    protected $shiftFunction;

    /**
     * @var int Request timeout for posting to rollbar, in seconds. default 3
     */
    protected $timeout = 3;

    /**
     * @var bool Register Rollbar as an exception handler to log PHP exceptions
     */
    protected $exceptionhandler;

    /**
     * @var bool Register Rollbar as an error handler to log PHP errors
     */
    protected $errorhandler;

    /**
     * @var bool Register Rollbar as an shutdown function
     */
    protected $shutdownfunction;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getClientAccessToken()
    {
        return $this->clientAccessToken;
    }

    /**
     * @param string $clientAccessToken
     */
    public function setClientAccessToken($clientAccessToken)
    {
        $this->clientAccessToken = $clientAccessToken;
    }

    /**
     * @return string
     */
    public function getBaseApiUrl()
    {
        return $this->baseApiUrl;
    }

    /**
     * @param string $baseApiUrl
     */
    public function setBaseApiUrl($baseApiUrl)
    {
        $this->baseApiUrl = $baseApiUrl;
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }

    /**
     * @param int $batchSize
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @return boolean
     */
    public function isBatched()
    {
        return $this->batched;
    }

    /**
     * @param boolean $batched
     */
    public function setBatched($batched)
    {
        $this->batched = $batched;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return boolean
     */
    public function isCaptureErrorBacktraces()
    {
        return $this->captureErrorBacktraces;
    }

    /**
     * @param boolean $captureErrorBacktraces
     */
    public function setCaptureErrorBacktraces($captureErrorBacktraces)
    {
        $this->captureErrorBacktraces = $captureErrorBacktraces;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return array
     */
    public function getErrorSampleRates()
    {
        return $this->errorSampleRates;
    }

    /**
     * @param array $errorSampleRates
     */
    public function setErrorSampleRates($errorSampleRates)
    {
        $this->errorSampleRates = $errorSampleRates;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string
     */
    public function getAgentLogLocation()
    {
        return $this->agentLogLocation;
    }

    /**
     * @param string $agentLogLocation
     */
    public function setAgentLogLocation($agentLogLocation)
    {
        $this->agentLogLocation = $agentLogLocation;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return \iRollbarLogger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param \iRollbarLogger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return int
     */
    public function getMaxErrno()
    {
        return $this->maxErrno;
    }

    /**
     * @param int $maxErrno
     */
    public function setMaxErrno($maxErrno)
    {
        $this->maxErrno = $maxErrno;
    }

    /**
     * @return array
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param array $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPersonFn()
    {
        return $this->personFn;
    }

    /**
     * @param mixed $personFn
     */
    public function setPersonFn($personFn)
    {
        $this->personFn = $personFn;
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param string $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * @return array
     */
    public function getScrubFields()
    {
        return $this->scrubFields;
    }

    /**
     * @param array $scrubFields
     */
    public function setScrubFields($scrubFields)
    {
        $this->scrubFields = $scrubFields;
    }

    /**
     * @return boolean
     */
    public function isShiftFunction()
    {
        return $this->shiftFunction;
    }

    /**
     * @param boolean $shiftFunction
     */
    public function setShiftFunction($shiftFunction)
    {
        $this->shiftFunction = $shiftFunction;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return boolean
     */
    public function isExceptionhandler()
    {
        return $this->exceptionhandler;
    }

    /**
     * @param boolean $exceptionhandler
     */
    public function setExceptionhandler($exceptionhandler)
    {
        $this->exceptionhandler = $exceptionhandler;
    }

    /**
     * @return boolean
     */
    public function isErrorhandler()
    {
        return $this->errorhandler;
    }

    /**
     * @param boolean $errorhandler
     */
    public function setErrorhandler($errorhandler)
    {
        $this->errorhandler = $errorhandler;
    }

    /**
     * @return boolean
     */
    public function isShutdownfunction()
    {
        return $this->shutdownfunction;
    }

    /**
     * @param boolean $shutdownfunction
     */
    public function setShutdownfunction($shutdownfunction)
    {
        $this->shutdownfunction = $shutdownfunction;
    }
}
