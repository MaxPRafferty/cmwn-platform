<?php

namespace FlipTest\Rule\Rule;

use Flip\Rule\Rule\EarnedFlipXTimes as Rule;
use Flip\EarnedFlip;
use Flip\Service\FlipUserServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;
use Zend\Paginator\Adapter\Iterator;

/**
 * Test EarnedFlipXTimesTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EarnedFlipXTimesTest extends TestCase
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
    public function testItShouldBeSatisifedWhenNumberOfFlipsMatchesExpectedTimesEarned()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator([$earnedFlip, $earnedFlip]));
        $this->assertEquals(2, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user',
            2
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
    public function testItShouldNotBeSatisfiedWhenTheUserHasInsufficientEarnings()
    {
        $earnedFlip = new EarnedFlip();
        $result     = new Iterator(new \ArrayIterator([$earnedFlip, $earnedFlip]));
        $this->assertEquals(2, $result->count());
        $user       = new Child();

        $this->userFlipService->shouldReceive('fetchFlipsForUser')
            ->with($user, 'foo-bar-flip')
            ->andReturn($result)
            ->once();

        $rule = new Rule(
            $this->userFlipService,
            'foo-bar-flip',
            'user',
            3
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            Rule::class . ' should not be happy since the user has not earned the flip 3 times'
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
            'user',
            3
        );

        $item = new BasicRuleItem(new BasicValueProvider('user', $user));

        $rule->isSatisfiedBy($item);
    }
}
