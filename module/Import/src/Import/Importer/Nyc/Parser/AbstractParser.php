<?php

namespace Import\Importer\Nyc\Parser;

use Import\ActionInterface;
use Import\ParserInterface;
use Security\Authorization\Rbac;
use Security\Authorization\RbacAwareInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * Class AbstractParser
 */
abstract class AbstractParser implements ParserInterface, LoggerAwareInterface, RbacAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected static $logger;

    /**
     * @var RbacAwareInterface
     */
    protected static $rbac;

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
     * @return Logger
     */
    public function getLogger()
    {
        if (static::$logger === null) {
            $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        }

        return static::$logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        static::$logger = $logger;
    }

    public function setRbac(Rbac $rbac)
    {
        static::$rbac = $rbac;
    }

    public function getRbac()
    {
        return self::$rbac;
    }

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
        $rowString      = $row !== null ? 'Row: <b>' . $row . '</b> ': "";
        $sheetString    = $sheet !== null ? 'Sheet <b>"' . $sheet . '"</b> ' : "";
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
        $rowString        = $row !== null ? 'Row: <b>' . $row . '</b> ' : "";
        $sheetString      = $sheet !== null ? 'Sheet <b>"' . $sheet . '"</b> ' : "";
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
