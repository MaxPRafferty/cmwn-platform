<?php

namespace Group\Service;

use Application\Utils\CreateDirectoryTrait;
use Dompdf\Dompdf;
use Group\Group;
use Group\GroupInterface;
use Group\UserCardModel\UserCardModel;
use iio\libmergepdf\Merger;
use User\UserInterface;
use Zend\View\Renderer\PhpRenderer;

/**
 * Service to generate user card pdfs
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
     * UserCardService constructor.
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupServiceInterface $groupService
     * @param PhpRenderer $renderer
     */
    public function __construct(
        UserGroupServiceInterface $userGroupService,
        GroupServiceInterface $groupService,
        PhpRenderer $renderer
    ) {
        $this->userGroupService = $userGroupService;
        $this->groupService = $groupService;
        $this->renderer = $renderer;
    }

    /**
     * @param GroupInterface $class
     * @param string $targetDirectory
     * @return string
     */
    protected function generateCardsForClass(GroupInterface $class, string $targetDirectory)
    {
        $teachers = $this->fetchUsersInGroup($class, ['role' => 'teacher']);
        $teachers = implode(', ', array_map(function ($teacher) {
            return $teacher->getFirstName() . ' ' . $teacher->getLastName();
        }, $teachers));

        $users = $this->fetchUsersInGroup($class);

        $fileName = $this->getHashedFileName($users);
        $fileName = $targetDirectory . "/$fileName.pdf";
        if (file_exists($fileName)) {
            $this->pdfFiles[] = $fileName;
            return $fileName;
        }
        $domPdf = new Dompdf();
        $userCardModel = new UserCardModel([]);

        $userCardModel->setVariables([
            'users' => $users,
            'domain' => 'www.ChangeMyWorldNow.com',
            'message' => 'For security reasons, you must reset your password and log in again',
            'float' => 'left',
            'teacherNames' => $teachers,
            'classTitle' => $class->getTitle()
        ]);
        $html = $this->renderer->render($userCardModel);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();
        $output = $domPdf->output();
        file_put_contents($fileName, $output);
        $this->pdfFiles[] = $fileName;
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
     * @inheritdoc
     */
    public function generateUserCards(GroupInterface $group)
    {
        $targetDirectory = __DIR__ . '/../../../../../tmp';
        $this->createDirectory($targetDirectory);

        $targetDirectory .= '/user-cards';
        $this->createDirectory($targetDirectory);

        $classes = $group->getType() === 'school'
            ? $this->groupService->fetchChildGroups($group, null, new Group())
            : [$group];

        $classes = is_array($classes) ? $classes : $classes->getItems(0, $classes->count());

        $fileName = '';
        array_walk($classes, function ($class) use ($targetDirectory, &$fileName) {
            $fileName = $this->generateCardsForClass($class, $targetDirectory);
        });

        if ($group->getType() === 'school') {
            $fileName = md5(serialize($this->pdfFiles));
            $fileName = $targetDirectory . "/$fileName.pdf";
            if (file_exists($fileName)) {
                //return $fileName;
                return;
            }

            $pdfMerger = new Merger();
            $pdfMerger->addIterator($this->pdfFiles);
            file_put_contents($fileName, $pdfMerger->merge());
            return $fileName;
        }

        //return $fileName;
    }
}
