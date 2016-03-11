<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\ActionInterface;
use Import\Importer\Nyc\Exception\InvalidDdbnnException;
use Import\ParserInterface;
use PHPExcel_Worksheet_RowCellIterator as CellIterator;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use \PHPExcel_Worksheet_Row as ExcelRow;

/**
 * Base Nyc DOE Excel Parser class
 *
 * Class AbstractParser
 * @package Import\Importer\Nyc\Parser
 */
abstract class AbstractParser implements LoggerAwareInterface, ParserInterface
{
    use LoggerAwareTrait;

    const SHEET_NAME = 'NYCDOE';

    /**
     * @var string[] errors that were generated during processing
     */
    protected $errors = [];

    /**
     * @var string[] warnings that generated during processing
     */
    protected $warnings = [];

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $workSheet;

    /**
     * @var \PHPExcel_WorksheetIterator
     */
    protected $iterator;

    /**
     * @var ActionInterface[] help children write actions
     */
    protected $actionList = [];

    /**
     * AbstractParser constructor.
     * @param \PHPExcel_Worksheet $worksheet
     */
    public function __construct(\PHPExcel_Worksheet $worksheet)
    {
        $this->workSheet = $worksheet;
        $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
    }

    /**
     * @return \PHPExcel_Worksheet_RowIterator
     */
    protected function getWorksheetIterator()
    {
        if ($this->iterator === null) {
            $this->iterator = new \PHPExcel_Worksheet_RowIterator($this->workSheet);
        }

        return $this->iterator;
    }

    /**
     * Gets the list of actions the parser has found
     *
     * @return \Import\ActionInterface[]
     */
    public function getActions()
    {
        return $this->actionList;
    }

    /**
     * Adds an action to the action list
     *
     * @param ActionInterface $action
     */
    protected function addAction(ActionInterface $action)
    {
        array_push($this->actionList, $action);
    }

    /**
     * Adds and error
     *
     * @param $message
     * @param $sheet
     * @param $row
     */
    protected function addError($message, $sheet, $row = null)
    {
        $rowString      = $row !== null ? ' Row: ' . $row : "";
        $errorMessage   = sprintf('Sheet "%s"%s %s', $sheet, $rowString, $message);
        $this->errors[] =  $errorMessage;
        $this->getLogger()->err($errorMessage);
    }

    /**
     * Adds a warning to the list
     *
     * @param $message
     * @param $sheet
     * @param $row
     */
    protected function addWarning($message, $sheet, $row)
    {
        $warningMessage   = sprintf('Sheet "%s" Row: %s %s', $sheet, $row, $message);
        $this->warnings[] =  $warningMessage;
        $this->getLogger()->warn($warningMessage);
    }

    /**
     * If there are warnings that did not prevent the parser from running but you will still like
     * to notify the user.
     *
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Called after preProcess checks if the parser has warnings or not
     *
     * @return mixed
     */
    public function hasWarnings()
    {
        return !empty($this->warnings);
    }

    /**
     * Called after preProcess to check to see if any errors came up during processing
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Gets a list of errors from the preProcessor
     *
     * If the preProcessor fails, this SHOULD return a list of errors
     * that will be sent back to the user to help them fix
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param ExcelRow $row
     * @param $start
     * @param $end
     * @return bool
     */
    protected function isRowEmpty(ExcelRow $row, $start, $end)
    {
        $cellIterator = $row->getCellIterator($start, $end);

        foreach ($cellIterator as $cell) {
            /** @var \PHPExcel_Cell $cell */
            if ($cell->getFormattedValue() !== "") {
                return false;
            }
        }

        return true;
    }

    /**
     * Parses the ddbnnn and returns a VO with the parts broken up
     *
     * @param $ddbnnn
     * @return \stdClass
     * @throws InvalidDdbnnException
     */
    public static function parseDdbnnn($ddbnnn)
    {
        preg_match('/^(\d{2})([A-Z])(\d{3})$/', $ddbnnn, $result);
        
        if (count($result) !== 4) {
            throw new InvalidDdbnnException();
        }

        $return = new \stdClass();
        $return->district = $result[1];
        $return->burough  = $result[2];
        $return->class    = $result[3];

        return $return;
    }

    /**
     * Gets the DDBNNN from the row
     *
     * @param CellIterator $cellIterator
     * @param int $rowNumber
     * @return bool|\stdClass
     */
    protected function getDdbnnn(CellIterator $cellIterator, $rowNumber)
    {
        $dString = $cellIterator->seek('A')->current()->getFormattedValue();

        try {
            $this->getLogger()->debug('Validating DDBNNN: ' . $dString);
            $ddbnnn = AbstractParser::parseDdbnnn($dString);
        } catch (InvalidDdbnnException $badNumber) {
            $this->addError(
                sprintf('Invalid DDBNNN "%s"', $dString),
                static::SHEET_NAME,
                $rowNumber
            );

            return false;
        }

        return $ddbnnn;
    }
}
