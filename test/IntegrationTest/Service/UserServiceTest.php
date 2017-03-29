<?php

namespace IntegrationTest\Service;

use Application\Exception\DuplicateEntryException;
use Application\Exception\NotFoundException;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use User\Adult;
use User\Child;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

/**
 * Test UserServiceTest
 *
 * @group User
 * @group Service
 * @group UserService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserServiceTest extends TestCase
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var UserInterface
     */
    protected $child;

    /**
     * @var UserInterface
     */
    protected $adult;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/users.dataset.php');
    }

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->logInUser('super_user');
        $this->userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpChild()
    {
        $this->child = new Child([
                'username'     => 'foo_student',
                'email'        => 'foo@ginasink.com',
                'type'         => 'CHILD',
                'first_name'   => 'John',
                'last_name'    => 'Yoder',
                'created'      => '2016-04-27 10:48:44',
                'updated'      => '2016-04-27 10:48:46',
            ]);
    }

    /**
     * @before
     */
    public function setUpAdult()
    {
        $this->adult = new Adult([
            'username'     => 'bar_teacher',
            'email'        => 'bar@ginasink.com',
            'type'         => 'ADULT',
            'first_name'   => 'Johnny',
            'last_name'    => 'Bar',
            'created'      => '2016-04-27 10:48:44',
            'updated'      => '2016-04-27 10:48:46',
        ]);
    }

    /**
     * @test
     */
    public function testItShouldFetchUserUsingId()
    {
        $user = $this->userService->fetchUser('english_student');
        $this->assertInstanceOf(Child::class, $user, 'Child was not returned from fetch');

        $expectedData = [
            'user_id'      => 'english_student',
            'username'     => 'english_student',
            'email'        => 'english_student@changemyworldnow.com',
            'type'         => 'CHILD',
            'first_name'   => 'John',
            'middle_name'  => 'D',
            'last_name'    => 'Yoder',
            'gender'       => 'M',
            'meta'         => [],
            'birthdate'    => '2016-04-15 11:58:15',
            'created'      => '2016-04-27 10:48:44',
            'updated'      => '2016-04-27 10:48:46',
            'deleted'      => null,
            'external_id'  => '8675309',
        ];

        $this->assertEquals($expectedData, $user->getArrayCopy(), 'Data was not set correctly from the database');
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByExternalId()
    {
        $user = $this->userService->fetchUserByExternalId(8675309);

        $this->assertInstanceOf(Child::class, $user, 'Child was not returned from fetch');

        $expectedData = [
            'user_id'      => 'english_student',
            'username'     => 'english_student',
            'email'        => 'english_student@changemyworldnow.com',
            'type'         => 'CHILD',
            'first_name'   => 'John',
            'middle_name'  => 'D',
            'last_name'    => 'Yoder',
            'gender'       => 'M',
            'meta'         => [],
            'birthdate'    => '2016-04-15 11:58:15',
            'created'      => '2016-04-27 10:48:44',
            'updated'      => '2016-04-27 10:48:46',
            'deleted'      => null,
            'external_id'  => '8675309',
        ];

        $this->assertEquals($expectedData, $user->getArrayCopy(), 'Data was not set correctly from the database');
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByEmail()
    {
        $user = $this->userService->fetchUserByEmail('english_student@ginasink.com');

        $this->assertInstanceOf(Child::class, $user, 'Child was not returned from fetch');

        $expectedData = [
            'user_id'      => 'english_student',
            'username'     => 'english_student',
            'email'        => 'english_student@changemyworldnow.com',
            'type'         => 'CHILD',
            'first_name'   => 'John',
            'middle_name'  => 'D',
            'last_name'    => 'Yoder',
            'gender'       => 'M',
            'meta'         => [],
            'birthdate'    => '2016-04-15 11:58:15',
            'created'      => '2016-04-27 10:48:44',
            'updated'      => '2016-04-27 10:48:46',
            'deleted'      => null,
            'external_id'  => '8675309',
        ];

        $this->assertEquals($expectedData, $user->getArrayCopy(), 'Data was not set correctly from the database');
    }

    /**
     * @test
     */
    public function testItShouldFetchUserByUsername()
    {
        $user = $this->userService->fetchUserByUsername('english_student');

        $this->assertInstanceOf(Child::class, $user, 'Child was not returned from fetch');

        $expectedData = [
            'user_id'      => 'english_student',
            'username'     => 'english_student',
            'email'        => 'english_student@changemyworldnow.com',
            'type'         => 'CHILD',
            'first_name'   => 'John',
            'middle_name'  => 'D',
            'last_name'    => 'Yoder',
            'gender'       => 'M',
            'meta'         => [],
            'birthdate'    => '2016-04-15 11:58:15',
            'created'      => '2016-04-27 10:48:44',
            'updated'      => '2016-04-27 10:48:46',
            'deleted'      => null,
            'external_id'  => '8675309',
        ];

        $this->assertEquals($expectedData, $user->getArrayCopy(), 'Data was not set correctly from the database');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->userService->fetchUser('foo_bar');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFoundByExternalId()
    {
        $this->expectException(NotFoundException::class);
        $this->userService->fetchUserByExternalId('foo_bar');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNotFoundByEmail()
    {
        $this->expectException(NotFoundException::class);
        $this->userService->fetchUserByEmail('test@example.com');
    }

    /**
     * @test
     * @todo verify db records are correct
     */
    public function testItShouldSoftDeleteUser()
    {
        $child = new Child();
        $child->setUserId('english_student');
        $this->userService->deleteUser($child);
        $this->expectException(NotFoundException::class);
        $this->userService->fetchUser($child->getUserId());
    }

    /**
     * @test
     * @todo verify records
     */
    public function testItShouldHardDeleteUser()
    {
        $child = new Child();
        $child->setUserId('english_student');
        $this->userService->deleteUser($child, true);
        $this->expectException(NotFoundException::class);
        $this->userService->fetchUser($child->getUserId());
    }

    /**
     * @test
     * @todo expand testing for fetch all
     */
    public function testItShouldFetchAllUsersWithNoWhere()
    {

        $adapter = $this->userService->fetchAll();
        $this->assertInstanceOf(DbSelect::class, $adapter);

        $paginator = new Paginator($adapter);
        $actualIds = [];
        foreach ($paginator as $user) {
            $this->assertInstanceOf(UserInterface::class, $user);
            array_push($actualIds, $user->getUserId());
        }

        $expectedIds = [
            'english_student',
            'english_teacher',
            'math_student',
            'math_teacher',
            'other_principal',
            'other_student',
            'other_teacher',
            'principal',
        ];

        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Incorrect records returned from fetch all');
    }

    /**
     * @test
     */
    public function testItShouldCreateChildRecord()
    {
        $this->userService->createUser($this->child);

        $this->assertInstanceOf(Child::class, $this->userService->fetchUser($this->child->getUserId()));
    }

    /**
     * @test
     */
    public function testItShouldCreateAdultRecord()
    {
        $this->userService->createUser($this->adult);

        $this->assertInstanceOf(Adult::class, $this->userService->fetchUser($this->adult->getUserId()));
    }

    /**
     * @test
     */
    public function testItShouldNotCreateChildRecordOnInvalidRecord()
    {
        $this->markTestSkipped('Until bug CORE-2543 is resolved, we are not checking duplicates for children');
        $this->userService->createUser($this->child);

        $this->expectException(DuplicateEntryException::class);
        $this->assertFalse($this->userService->createUser($this->child));
    }

    /**
     * @test
     */
    public function testItShouldCreateAdultRecordOnInvalidRecord()
    {
        $this->userService->createUser($this->adult);

        $this->expectException(DuplicateEntryException::class);
        $this->assertFalse($this->userService->createUser($this->adult));
    }

    /**
     * @test
     */
    public function testItShouldUpdateAdult()
    {
        $original = $this->userService->fetchUser('english_teacher');
        $original->setFirstName('Chuck');

        $this->assertTrue($this->userService->updateUser($original));

        $changed = $this->userService->fetchUser('english_teacher');
        $this->assertEquals('Chuck', $changed->getFirstName());
    }

    /**
     * @test
     */
    public function testItShouldNotUpdateAdultWhenUserInvalid()
    {
        $this->expectException(NotFoundException::class);
        $user = new Adult();
        $user->setUserId('englihs_teacher');
        $this->assertFalse($this->userService->updateUser($user));
    }

    /**
     * @test
     * @ticket CORE-645
     */
    public function testItShouldNotAllowUsersToChangeType()
    {
        $original = $this->userService->fetchUser('english_student');
        $this->assertInstanceOf(Child::class, $original);

        $user = new Adult($original->getArrayCopy());
        $this->assertEquals(UserInterface::TYPE_ADULT, $user->getType());

        $this->userService->updateUser($user);

        $changed = $this->userService->fetchUser('english_student');
        $this->assertInstanceOf(Child::class, $changed, 'The user type was changed');
    }
}
