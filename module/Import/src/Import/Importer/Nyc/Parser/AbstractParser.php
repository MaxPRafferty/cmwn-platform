<?php

namespace Import\Importer\Nyc\Parser;

use Application\Utils\NoopLoggerAwareTrait;
use Import\ActionInterface;
use Import\ParserInterface;
use Zend\Log\LoggerAwareInterface;

/**
 * Class AbstractProcesser
 */
abstract class AbstractParser implements ParserInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var string[] errors that were generated during processing
     */
    protected static $errors = [];

    /**
     * @var string[] warnings that generated during processing
     */
    protected static $warnings = [];

    /**
     * @var \SplPriorityQueue|ActionInterface[] help children write actions
     */
    protected static $actionList;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $workSheet;

    /**
     * @var \PHPExcel_WorksheetIterator
     */
    protected $iterator;

    /**
     * Gets the list of actions the parser has found
     *
     * @return \SplPriorityQueue|\Import\ActionInterface[]
     */
    public function getActions()
    {
        return self::$actionList;
    }

    /**
     * Adds an action to the action list
     *
     * @param ActionInterface $action
     */
    public static function addAction(ActionInterface $action)
    {
        if (self::$actionList === null) {
            self::$actionList = new \SplPriorityQueue();
        }

        self::$actionList->insert($action, $action->priority());
    }

    /**
     * Clears the action queue
     *
     * Mainly used for testing
     */
    public static function clear()
    {
        self::$actionList = new \SplPriorityQueue();
        self::$errors     = [];
        self::$warnings   = [];
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
        self::$errors[] =  $errorMessage;
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
        self::$warnings[] =  $warningMessage;
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
        return self::$warnings;
    }

    /**
     * Called after preProcess checks if the parser has warnings or not
     *
     * @return mixed
     */
    public function hasWarnings()
    {
        return !empty(self::$warnings);
    }

    /**
     * Called after preProcess to check to see if any errors came up during processing
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty(self::$errors);
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
        return self::$errors;
    }
}
