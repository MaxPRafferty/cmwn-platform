<?php

namespace Import\Importer\Nyc\Parser;

use Import\ActionInterface;
use Import\ParserInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;

/**
 * Class AbstractProcesser
 *
 * ${CARET}
 */
abstract class AbstractParser implements ParserInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

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
    protected function addError($message, $sheet = null, $row = null)
    {
        $rowString      = $row !== null ? 'Row: ' . $row . ' ': "";
        $sheetString    = $sheet !== null ? 'Sheet "' . $sheet . '" ' : "";
        $errorMessage   = sprintf('%s%s%s', $sheetString, $rowString, $message);
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
    protected function addWarning($message, $sheet = null, $row = null)
    {
        $rowString        = $row !== null ? 'Row: ' . $row . ' ' : "";
        $sheetString      = $sheet !== null ? 'Sheet "' . $sheet . '" ' : "";
        $warningMessage   = sprintf('%s%s%s', $sheetString, $rowString, $message);
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
}
