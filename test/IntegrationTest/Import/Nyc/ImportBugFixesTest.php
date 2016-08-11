<?php

namespace IntegrationTest\Import\Nyc;

use Group\Group;
use Import\Importer\Nyc\DoeImporter;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\Authentication\AuthenticationService;
use Security\Authorization\Rbac;
use Security\Service\SecurityService;
use Zend\Json\Json;
use Zend\Log\Logger;
use Zend\Paginator\Paginator;

/**
 * Test ImportBugFixesTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ImportBugFixesTest extends TestCase
{
    /**
     * @var DoeImporter
     */
    protected $importer;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../DataSets/same-district.import.dataset.php';

        return new ArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpLoggedInUser()
    {
        /** @var SecurityService $userService */
        $userService = TestHelper::getServiceManager()->get(SecurityService::class);
        $user        = $userService->fetchUserByUserName('super_user');
        $authService = TestHelper::getServiceManager()->get(AuthenticationService::class);
        $authService->getStorage()->write($user);
    }

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = NycDoeTestImporterSetup::getImporter();
        $this->importer->exchangeArray([
            'file'         => __DIR__ . '/_files/hogwarts.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'hogwarts',
            'email'        => 'test@example.com',
        ]);

        $this->importer->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
    }

    /**
     * @return \User\Service\UserServiceInterface
     */
    public function getUserService()
    {
        return NycDoeTestImporterSetup::getUserService();
    }

    /**
     * @return \Group\Service\UserGroupServiceInterface
     */
    public function getUserGroupService()
    {
        return NycDoeTestImporterSetup::getUserGroupService();
    }

    /**
     * @return \Group\Service\GroupServiceInterface
     */
    public function getGroupService()
    {
        return NycDoeTestImporterSetup::getGroupService();
    }

    /**
     * @test
     * @ticket CORE-1070
     */
    public function testItShouldNotShowUsersFromDifferentSchoolForAdministrator()
    {
        $this->assertEmpty($this->importer->perform());

        // test the teacher from the default school
        $usersForEnglishTeacher = new Paginator($this->getUserGroupService()->fetchAllUsersForUser('english_teacher'));

        $expectedUsers = [
            'english_student',
            'principal',
        ];

        $actualUsers = [];

        foreach ($usersForEnglishTeacher as $user) {
            array_push($actualUsers, $user->getUserId());
        }

        $this->assertEquals(
            $expectedUsers,
            $actualUsers,
            'English teacher is seeing the wrong users'
        );
    }

    /**
     * @test
     * @ticket CORE-1116
     */
    public function testItShouldNotShowClassesForStudent()
    {
        $this->assertEmpty($this->importer->perform());

        // test the english student from the default school
        $classesForEnglishStudent = new Paginator(
            $this->getUserGroupService()->fetchGroupsForUser('english_student', null, new Group())
        );

        $expectedGroups = [
            'english',
            'school',
        ];

        $actualGroups = [];

        foreach ($classesForEnglishStudent as $group) {
            array_push($actualGroups, $group->getGroupId());
        }

        $this->assertEquals(
            $expectedGroups,
            $actualGroups,
            'English student is seeing the wrong groups'
        );
    }

    /**
     * @test
     * @ticket CORE-1117
     */
    public function testItShouldNotAllowStudentsToViewStudentsNotInTheirClass()
    {
        $this->assertEmpty($this->importer->perform());

        $hogwartsStudent = $this->getUserService()->fetchUserByExternalId('01C123-0001');

        $this->logInUser($hogwartsStudent->getUserName());
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student');

        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-1071
     */
    public function testItShouldNotAllowTeacherToEditPrincipal()
    {
        $this->logInUser('english_teacher');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/principal', 'PUT', []);

        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-1071
     */
    public function testItShouldAllowPrincipalToEditTeacher()
    {
        $this->logInUser('principal');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_teacher', 'PUT', []);

        $this->assertResponseStatusCode(422); // we are just checking access here
    }

    /**
     * @test
     * @ticket CORE-1071
     */
    public function testItShouldSetCorrectScope()
    {
        $this->logInUser('principal');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_teacher', 'PUT', []);

        $this->assertResponseStatusCode(422); // we are just checking access here
    }

    /**
     * @test
     */
    public function testItShouldReturnCorrectScopeForTeacherWhenPrincipal()
    {
        $this->logInUser('principal');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_teacher');

        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('scope', $body);

        $this->assertEquals(Rbac::SCOPE_REMOVE | Rbac::SCOPE_UPDATE, $body['scope']);
    }

    /**
     * @test
     */
    public function testItShouldReturnCorrectScopeForPrincipalWhenTeacher()
    {
        $this->logInUser('english_teacher');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/principal');

        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('scope', $body);

        $this->assertEquals(0, $body['scope']);
    }
}
