<?php

namespace Import\Importer\Nyc;

use Group\Service\GroupServiceInterface;
use Import\ActionInterface;
use Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser;
use Import\Importer\Nyc\Parser\Excel\StudentWorksheetParser;
use Import\Importer\Nyc\Parser\Excel\TeacherWorksheetParser;
use Import\ParserInterface;
use User\Service\UserServiceInterface;

/**
 * Class DoePreProcessor
 */
class DoeProcessor implements ParserInterface
{
    /**
     * @var ClassWorksheetParser
     */
    protected $classProcessor;

    /**
     * @var TeacherWorksheetParser
     */
    protected $teacherProcessor;

    /**
     * @var StudentWorksheetParser
     */
    protected $studentProcessor;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var string
     */
    protected $fileName;

    public function __construct(UserServiceInterface $userService, GroupServiceInterface $groupService)
    {
        $this->userService  = $userService;
        $this->groupService = $groupService;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * PreProcess a file
     */
    public function preProcess()
    {
        if ($this->fileName === null) {
            
        }
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
        // TODO: Implement getErrors() method.
    }

    /**
     * Called after preProcess to check to see if any errors came up during processing
     *
     * @return bool
     */
    public function hasErrors()
    {
        // TODO: Implement hasErrors() method.
    }

    /**
     * If there are warnings that did not prevent the parser from running but you will still like
     * to notify the user.
     *
     * @return string[]
     */
    public function getWarnings()
    {
        // TODO: Implement getWarnings() method.
    }

    /**
     * Called after preProcess checks if the parser has errors or not
     *
     * @return mixed
     */
    public function hasWarnings()
    {
        // TODO: Implement hasWarnings() method.
    }

    /**
     * Gets list of actions the parser has discovered it needs to run
     *
     * @return ActionInterface[]
     */
    public function getActions()
    {
        // TODO: Implement getActions() method.
    }


}
