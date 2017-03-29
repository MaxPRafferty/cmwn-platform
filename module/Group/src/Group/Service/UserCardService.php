<?php

namespace Group\Service;

use Application\Utils\CreateDirectoryTrait;
use Dompdf\Dompdf;
use Group\Group;
use Group\GroupInterface;
use Group\UserCardModel\DefaultModel;
use Group\UserCardModel\UserCardModel;
use iio\libmergepdf\Merger;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\View\Renderer\PhpRenderer;

/**
 * Service to generate user card pdfs
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserCardService implements UserCardServiceInterface
{
    use CreateDirectoryTrait;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $pdfFiles = [];

    /**
     * @var string
     */
    protected $targetDirectory;

    /**
     * @var string
     */
    protected $pdfDirectory;

    /**
     * UserCardService constructor.
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupServiceInterface $groupService
     * @param PhpRenderer $renderer
     * @param string $targetDirectory
     */
    public function __construct(
        UserGroupServiceInterface $userGroupService,
        GroupServiceInterface $groupService,
        PhpRenderer $renderer,
        $targetDirectory = __DIR__ . '/../../../../../tmp'
    ) {
        $this->userGroupService = $userGroupService;
        $this->groupService = $groupService;
        $this->renderer = $renderer;
        $this->targetDirectory = $targetDirectory;
        $this->pdfDirectory = $targetDirectory . '/user-cards';
    }

    /**
     * @param $model
     * @param $fileName
     */
    protected function createPdfFile($model, $fileName)
    {
        $domPdf = new Dompdf();
        $html = $this->renderer->render($model);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $output = $domPdf->output();
        file_put_contents($fileName, $output);
    }

    /**
     * @return string
     */
    protected function getDefaultPdf()
    {
        $fileName = $this->pdfDirectory . '/default.pdf';

        if (file_exists($fileName)) {
            return $fileName;
        }

        $defaultModel = new DefaultModel();
        $this->createPdfFile($defaultModel, $fileName);
        return $fileName;
    }

    /**
     * @param $group
     * @param null $where
     * @return array
     */
    protected function fetchUsersInGroup($group, $where = null)
    {
        $users = $this->userGroupService->fetchUsersForGroup($group, $where);

        return $users->getItems(0, $users->count());
    }

    /**
     * @param array $users
     * @return string
     */
    protected function getHashedFileName(array $users)
    {
        $userNames = [];
        array_walk($users, function ($user) use (&$userNames) {
            if (!$user->getType() === UserInterface::TYPE_CHILD) {
                return;
            }

            $userNames[] = $user->getUserName();
        });

        return md5(serialize($userNames));
    }

    /**
     * @param GroupInterface $class
     * @return string
     */
    protected function generateCardsForClass(GroupInterface $class)
    {
        $predicate = new PredicateSet();
        $predicate->addPredicate(new Operator('ug.role', '=', 'teacher'));
        $teachers = $this->fetchUsersInGroup($class, $predicate);
        $teachers = implode(', ', array_map(function ($teacher) {
            return $teacher->getFirstName() . ' ' . $teacher->getLastName();
        }, $teachers));

        $users = $this->fetchUsersInGroup($class);

        if (empty($users)) {
            return $this->getDefaultPdf();
        }

        $fileName = $this->getHashedFileName($users);
        $fileName = $this->pdfDirectory . "/$fileName.pdf";
        if (file_exists($fileName)) {
            $this->pdfFiles[] = $fileName;
            return $fileName;
        }

        $userCardModel = new UserCardModel([]);

        $userCardModel->setVariables([
            'users' => $users,
            'domain' => 'www.ChangeMyWorldNow.com',
            'message' => 'For security reasons, you must reset your password and log in again',
            'float' => 'left',
            'teacherNames' => $teachers,
            'classTitle' => $class->getTitle()
        ]);

        $this->createPdfFile($userCardModel, $fileName);

        $this->pdfFiles[] = $fileName;
        return $fileName;
    }

    /**
     * @param GroupInterface $school
     * @return null|string
     */
    protected function generateCardsForSchool(GroupInterface $school)
    {
        $classes = $this->groupService->fetchChildGroups($school, null, new Group());
        $classes = $classes->getItems(0, $classes->count());

        array_walk($classes, function ($class) {
            $this->generateCardsForClass($class);
        });

        if (empty($this->pdfFiles)) {
            return $this->getDefaultPdf();
        }

        $fileName = $this->pdfDirectory . '/' . md5(serialize($this->pdfFiles)) . '.pdf';
        if (file_exists($fileName)) {
            return $fileName;
        }

        $pdfMerger = new Merger();
        $pdfMerger->addIterator($this->pdfFiles);
        $pdf = $pdfMerger->merge();
        file_put_contents($fileName, $pdf);
        return $fileName;
    }

    /**
     * @inheritdoc
     */
    public function generateUserCards(GroupInterface $group)
    {
        $this->createDirectory($this->targetDirectory);

        $this->createDirectory($this->pdfDirectory);

        $fileName = $group->getType() === 'school'
            ? $this->generateCardsForSchool($group)
            : $this->generateCardsForClass($group);
        if (!$fileName) {
            return false;
        }

        return realpath($fileName);
    }
}
