<?php

namespace Import\Importer\Nyc\Parser;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Import\ActionInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoom;

/**
 * Class AddClassToSchoolAction
 */
class AddClassToSchoolAction implements ActionInterface
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var GroupInterface
     */
    protected $school;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * AddClassToSchoolAction constructor.
     *
     * @param GroupInterface $school
     * @param ClassRoom $classRoom
     * @param GroupServiceInterface $groupService
     */
    public function __construct(GroupInterface $school, ClassRoom $classRoom, GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
        $this->school       = $school;
        $this->classRoom    = $classRoom;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return sprintf(
            'Adding class room "%s" to school "%s"',
            $this->classRoom->getTitle(),
            $this->school->getTitle()
        );
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $this->groupService->attachChildToGroup($this->school, $this->classRoom->getGroup());
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 1;
    }
}
