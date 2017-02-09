<?php

namespace UserTest;

use PHPUnit\Framework\TestCase;
use User\Adult;
use User\Child;
use User\UserHydrator;

/**
 * Test UserHydratorTest
 *
 * @group Hydrator
 * @group User
 */
class UserHydratorTest extends TestCase
{
    /**
     * @var array
     */
    protected $userData = [];

    /**
     * @before
     */
    public function setUpData()
    {
        $date           = new \DateTime();
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

    /**
     * @test
     */
    public function testItShouldHydrateUsingTypeWhenPrototypeNotSet()
    {
        $hydrator = new UserHydrator();
        $user     = $hydrator->hydrate($this->userData, new \stdClass());
        $this->assertInstanceOf('User\Adult', $user);
    }

    /**
     * @test
     */
    public function testItShouldUsePrototypeWhenSet()
    {
        $hydrator = new UserHydrator(new Child());
        $user     = $hydrator->hydrate($this->userData, new \stdClass());
        $this->assertInstanceOf(Adult::class, $user);
    }

    /**
     * @test
     */
    public function testItShouldExtractUser()
    {
        $user     = new Adult($this->userData);
        $hydrator = new UserHydrator();
        $this->assertEquals($this->userData, $hydrator->extract($user));
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenNonUserPassedAsPrototype()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This Hydrator can only hydrate Users');
        new UserHydrator(new \stdClass());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToExtractNonUser()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('This Hydrator can only extract Users');
        $hydrator = new UserHydrator();
        $hydrator->extract(new \stdClass());
    }
}
