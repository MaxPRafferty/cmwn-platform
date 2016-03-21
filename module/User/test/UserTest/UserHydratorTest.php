<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use User\Child;
use User\UserHydrator;

/**
 * Test UserHydratorTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class UserHydratorTest extends TestCase
{
    protected $userData = [];

    /**
     * @before
     */
    public function setUpData()
    {
        $date = new \DateTime();
        $this->userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => 'male',
            'birthdate'   => $date->format("Y-m-d H:i:s"),
            'created'     => $date->format("Y-m-d H:i:s"),
            'updated'     => $date->format("Y-m-d H:i:s"),
            'deleted'     => $date->format("Y-m-d H:i:s"),
            'type'        => 'ADULT',
            'meta'        => [],
            'external_id' => 'foo-bar',
        ];
    }

    public function testItShouldHydrateUsingTypeWhenPrototypeNotSet()
    {
        $hydrator = new UserHydrator();
        $user     = $hydrator->hydrate($this->userData, new \stdClass());
        $this->assertInstanceOf('User\Adult', $user);
    }

    public function testItShouldUsePrototypeWhenSet()
    {
        $hydrator = new UserHydrator(new Child());
        $user     = $hydrator->hydrate($this->userData, new \stdClass());
        $this->assertInstanceOf('User\Child', $user);
    }

    public function testItShouldExtractUser()
    {
        $user     = new Adult($this->userData);
        $hydrator = new UserHydrator();
        $this->assertEquals($this->userData, $hydrator->extract($user));
    }

    public function testItShouldThrowExceptionWhenNonUserPassedAsPrototype()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'This Hydrator can only hydrate Users'
        );
        $hydrator = new UserHydrator(new \stdClass());
    }

    public function testItShouldThrowExceptionWhenTryingToExtractNonUser()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'This Hydrator can only extract Users'
        );
        $hydrator = new UserHydrator();
        $hydrator->extract(new \stdClass());
    }
}
