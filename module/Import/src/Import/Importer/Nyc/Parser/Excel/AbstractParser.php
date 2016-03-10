<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\Exception\InvalidDdbnnException;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;

/**
 * Base Nyc DOE Excel Parser class
 *
 * Class AbstractParser
 * @package Import\Importer\Nyc\Parser
 */
abstract class AbstractParser implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const INVALID_DDBNNN = 'Invalid DDBNNN';

    /**
     * @var string[] errors that were generated during processing
     */
    protected $errors = [];

    /**
     * @param $message
     * @param $sheet
     * @param $row
     */
    protected function addError($message, $sheet, $row)
    {
        $errorMessage   = sprintf('Sheet %s Row %s %s', $sheet, $row, $message);
        $this->errors[] =  $errorMessage;
    }

    /**
     * Tests weather the parser found errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Parses the ddbnnn and returns a VO with the parts broken up
     *
     * @param $ddbnnn
     * @return \stdClass
     * @throws InvalidDdbnnException
     */
    protected function parseDdbnnn($ddbnnn)
    {
        $result = preg_split('/(\d{2})([A-Z])(\d{3})/i', $ddbnnn);
        $this->getLogger()->debug('Validating DDBNNN: ' . $ddbnnn);
        if (count($result) !== 2) {
            $this->getLogger()->debug('Invalid DDBNNN');
            throw new InvalidDdbnnException();
        }

        $return = new \stdClass();
        $return->district = $result[0];
        $return->burough  = $result[1];
        $return->class    = $result[3];

        return $return;
    }
}
