<?php

namespace FlipTest\Rule\Rule;

use Flip\Rule\Rule\EarnedFlip as Rule;
use Flip\EarnedFlip;
use Flip\Service\FlipUserServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test EarnedFlipTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EarnedFlipTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|FlipUserServiceInterface
     */
    protected $userFlipService;

    /**
     * @before
     */
    public function setUpFlipUserService()
    {
        $this->userFlipService = \Mockery::mock(FlipUserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserHasEarnedFlipOnce()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator(array_fill(0, 1, $earnedFlip)));
        $this->assertEquals(1, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should be happy since the user earned the flip once'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            Rule::class . ' did not report the rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserHasEarnedFlipsTwice()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator(array_fill(0, 2, $earnedFlip)));
        $this->assertEquals(2, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should be happy since the user earned the flip twice'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            Rule::class . ' did not report the rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserHasEarnedFlipsThreeTimesALady()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator(array_fill(0, 3, $earnedFlip)));
        $this->assertEquals(3, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should be happy since the user earned the flip three times a lady'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            Rule::class . ' did not report the rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserHasEarnedFlipsAlot()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator(array_fill(0, 1000, $earnedFlip)));
        $this->assertEquals(1000, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should be happy since the user earned the flip three times a lady'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            Rule::class . ' did not report the rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisifedIfTheUserHasNotEarnedTheFlip()
    {
        $result     = new Iterator(new \ArrayIterator([]));
        $this->assertEquals(0, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should not be happy since the user has not earned the flip'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            Rule::class . ' did not report the rule was satisfied'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTheUserIsTheIncorrectType()
    {
        $this->expectException(InvalidProviderType::class);
        $user       = new \stdClass();
        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->never();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $rule->isSatisfiedBy($item);
    }
}
