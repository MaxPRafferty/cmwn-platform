<?php

namespace Import\Importer\Nyc;

use Import\Importer\Nyc\Parser\DoeParser;
use Job\JobInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Log\Writer\Noop;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class DoeImporter implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string the file name to process
     */
    protected $fileName;

    /**
     * @var DoeParser
     */
    protected $parser;

    /**
     * NycDoeImporter constructor.
     */
    public function __construct(DoeParser $parser)
    {
        $this->setLogger(new Logger(['writers' => [new Noop()]]));
        $this->parser = $parser;
    }

    
}
