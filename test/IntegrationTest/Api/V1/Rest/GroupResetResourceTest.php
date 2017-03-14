<?php


namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\SecurityUser;
use Security\Service\SecurityServiceInterface;

/**
 * Class GroupResetResourceTest
 * @group Db
 * @group IntegrationTest
 * @group GroupResetResource
 * @group Api
 * @package IntegrationTest\Api\V1\Rest
 */
class GroupResetResourceTest extends TestCase
{
    /**
     * @var SecurityServiceInterface $securityService
     */
    protected $securityService;

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->securityService = TestHelper::getServiceManager()->get(SecurityServiceInterface::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/group.reset.dataset.php');
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePassException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        $this->assertChangePasswordException('/group/school/reset', 'POST', ['code' => 'foobar12']);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/group/school/reset', 'POST', ['code' => 'foobar12']);
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     * @param $user
     * @dataProvider unauthorizedUserDataProvider
     */
    public function testItShouldNotAllowOthersToResetCodeForGroup($user, $groups)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        foreach ($groups as $group) {
            $this->dispatch('/group/' . $group .'/reset', 'POST', ['code' => 'foobar12']);
            $this->assertResponseStatusCode(403);
        }
    }

    /**
     * @test
     */
    public function testItShouldCheckIfCodeIsValid()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/group/school/reset', 'POST', ['code' => 'foobar']);
        $this->assertResponseStatusCode(422);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfCodeIsBeingPassedWhileRouting()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/group/school/reset', 'POST');
        $this->assertResponseStatusCode(422);
    }

    /**
     * @test
     * @param $login
     * @param $group
     * @param $users
     * @param $unchanged
     * @dataProvider userDataProvider
     */
    public function testItShouldResetCodeForGroups($login, $group, $users, $unchanged)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/group/' . $group . '/reset', 'POST', ['code' => 'apple007']);
        $this->assertControllerName('api\v1\rest\groupreset\controller');
        $this->assertMatchedRouteName('api.rest.group-reset');
        $this->assertResponseStatusCode(200);

        /**@var SecurityUser*/
        foreach ($users as $user) {
            $user = $this->securityService->fetchUserByUserName($user);
            $this->assertEquals(SecurityUser::CODE_VALID, $user->compareCode('apple007'));
        }

        /**@var SecurityUser*/
        foreach ($unchanged as $user) {
            $user = $this->securityService->fetchUserByUserName($user);
            $this->assertNotEquals(SecurityUser::CODE_VALID, $user->compareCode('apple007'));
        }
    }

    /**
     * @return array
     */
    public function unauthorizedUserDataProvider()
    {
        return [
            0 => ['english_student', ['school', 'english', 'math', 'other_school', 'other_math']],
            1 => ['english_teacher', ['math', 'other_school', 'other_math']],
            2 => ['other_student', ['school', 'english', 'math', 'other_school', 'other_math']],
            3 => ['other_teacher', ['school', 'english', 'math']],
            4 => ['principal', ['other_school', 'other_math']],
        ];
    }

    /**
     * @return array
     */
    public function userDataProvider()
    {
        return [
            0 => [
                'super_user',
                'school',
                ['english_student_code', 'math_student_code'],
                [
                    'english_student',
                    'math_student',
                    'other_student',
                    'other_student_code',
                    'english_teacher',
                    'math_teacher',
                    'other_teacher',
                    'principal',
                    'other_principal'
                ]
            ],
            1 => [
                'english_teacher',
                'english',
                ['english_student_code'],
                [
                    'english_student',
                    'math_student',
                    'math_student_code',
                    'other_student',
                    'other_student_code',
                    'english_teacher',
                    'math_teacher',
                    'other_teacher',
                    'principal',
                    'other_principal'
                ],
            ],
            2 => [
                'other_teacher',
                'other_school',
                ['other_student_code'],
                [
                    'english_student',
                    'english_student_code',
                    'math_student',
                    'math_student_code',
                    'other_student',
                    'english_teacher',
                    'math_teacher',
                    'other_teacher',
                    'principal',
                    'other_principal'
                ],
            ],
            3 => [
                'math_teacher',
                'math',
                ['math_student_code'],
                [
                    'english_student',
                    'english_student_code',
                    'math_student',
                    'other_student_code',
                    'other_student',
                    'english_teacher',
                    'math_teacher',
                    'other_teacher',
                    'principal',
                    'other_principal'
                ]
            ],
        ];
    }
}
