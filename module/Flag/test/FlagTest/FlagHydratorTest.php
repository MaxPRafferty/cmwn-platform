<?php

namespace FlagTest;

use Application\Exception\NotFoundException;
use Flag\Flag;
use Flag\FlagHydrator;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use User\Child;
use User\Service\UserServiceInterface;
use User\UserInterface;

/**
 * Class FlagHydratorTest
 * @package FlagTest
 * @group Flag
 * @group Hydrator
 * @group FlagHydrator
 * @group API
 * @group UnitTest
 */
class FlagHydratorTest extends TestCase
{
    /**
     * @var FlagHydrator
     */
    protected $hydrator;

    /**
     * @var \Mockery\MockInterface | UserServiceInterface
     */
    protected $userService;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var UserInterface
     */
    protected $flagger;

    /**
     * @var UserInterface
     */
    protected $flaggee;

    /**
     * @before
     */
    public function setUpHydrator()
    {
        $this->userService = \Mockery::mock('User\Service\UserService');
        $this->hydrator = new FlagHydrator($this->userService);
    }

    /**
     * @before
     */
    public function setUpFlagger()
    {
        $this->flagger = new Adult([
            'user_id'     => 'ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => 'male',
            'birthdate'   => '',
            'created'     => '',
            'updated'     => '',
            'deleted'     => '',
            'type'        => 'ADULT',
            'meta'        => [],
            'external_id' => 'foo-bar',
        ]);
    }

    /**
     * @before
     */
    public function setUpFlaggee()
    {
        $this->flaggee = new Adult([
            'user_id'     => 'abcd-efgh',
            'username'    => 'manchuck',
            'email'       => 'chaithra@ginasink.com',
            'first_name'  => 'Chaithra',
            'middle_name' => '',
            'last_name'   => 'Yenikapati',
            'gender'      => 'female',
            'birthdate'   => '',
            'created'     => '',
            'updated'     => '',
            'deleted'     => '',
            'type'        => 'ADULT',
            'meta'        => [],
            'external_id' => 'baz-bat',
        ]);
    }

    /**
     * @before
     */
    public function setUpData()
    {
        $this->data = [
            'flag_id' => 'qwerty',
            'flagger' => 'ijklm-nop',
            'flaggee' => 'abcd-efgh',
            'url'     => '/foo',
            'reason'  => 'bar',
        ];
    }

    /**
     * @test
     */
    public function testItShouldHydrateFlag()
    {
        $flag = new Flag();
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('ijklm-nop')
            ->andReturn($this->flagger);
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('abcd-efgh')
            ->andReturn($this->flaggee);
        $this->hydrator->hydrate($this->data, $flag);
        $this->assertEquals($flag->getFlagId(), 'qwerty');
        $this->assertEquals($flag->getFlagger()->getUserId(), 'ijklm-nop');
        $this->assertEquals($flag->getFlaggee()->getUserId(), 'abcd-efgh');
        $this->assertEquals($flag->getUrl(), '/foo');
        $this->assertEquals($flag->getReason(), 'bar');
    }

    /**
     * @test
     */
    public function testItShouldExtractFlag()
    {
        $array = array_merge(
            $this->data,
            ['flagger' => $this->flagger, 'flaggee' => $this->flaggee]
        );
        $flag = new Flag($array);
        $data = $this->hydrator->extract($flag);
        $this->assertEquals(
            $data,
            array_merge(
                $this->data,
                ['flagger' => $this->flagger->getArrayCopy(), 'flaggee' => $this->flaggee->getArrayCopy()]
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExtractNonFlagObject()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'This Hydrator can only extract Flags');
        $this->hydrator->extract(new \stdClass());
    }

    /**
     * @test
     */
    public function testItShouldNotHydrateNonFlagObject()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'This Hydrator can only hydrate Flags');
        $this->hydrator->hydrate($this->data, new \stdClass());
    }

    /**
     * @test
     */
    public function testItShouldDefaultToNullFlaggerAndFlaggeeWhenUsersNotFound()
    {
        $flag = new Flag();
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('ijklm-nop')
            ->andThrow(NotFoundException::class);
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('abcd-efgh')
            ->andThrow(NotFoundException::class);
        $this->hydrator->hydrate($this->data, $flag);
        $this->assertEquals($flag->getFlagId(), 'qwerty');
        $this->assertEquals($flag->getFlagger(), null);
        $this->assertEquals($flag->getFlaggee(), null);
        $this->assertEquals($flag->getUrl(), '/foo');
        $this->assertEquals($flag->getReason(), 'bar');
    }

    /**
     * @test
     */
    public function testItShouldNoCallsShouldBeMadeToFetchUserWhenFlaggerAndFlaggeeAreUsers()
    {
        $flag = new Flag();
        $data = $this->data;
        $data['flagger'] = new Child(['user_id' => 'english_student']);
        $data['flaggee'] = new Child(['user_id' => 'math_student']);
        $this->userService->shouldReceive('fetchUser')
            ->never();
        $this->hydrator->hydrate($data, $flag);
        $this->assertEquals($flag->getFlagId(), 'qwerty');
        $this->assertEquals($flag->getFlagger(), $data['flagger']);
        $this->assertEquals($flag->getFlaggee(), $data['flaggee']);
        $this->assertEquals($flag->getUrl(), '/foo');
        $this->assertEquals($flag->getReason(), 'bar');
    }
}
