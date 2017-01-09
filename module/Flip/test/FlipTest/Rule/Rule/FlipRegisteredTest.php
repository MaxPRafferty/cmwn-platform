<?php

namespace FlipTest\Rule\Rule;

use Application\Exception\NotFoundException;
use Flip\Flip;
use Flip\Rule\Rule\FlipRegistered as Rule;
use Flip\Service\FlipServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;

/**
 * Test FlipRegisteredTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipRegisteredTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|FlipServiceInterface
     */
    protected $flipService;

    /**
     * @before
     */
    public function setUpFlipService()
    {
        $this->flipService = \Mockery::mock(FlipServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenFlipExists()
    {
        $rule = new Rule(
            $this->flipService,
            'foo-bar-flip'
        );

        $this->flipService->shouldReceive('fetchFlipById')
            ->with('foo-bar-flip')
            ->andReturn(new Flip(['flip_id' => 'foo-bar-flip']))
            ->once();

        $this->assertTrue(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            Rule::class . ' is not satisfied when flip is registered'
        );

        $this->assertEquals(
            1,
            $rule->timesSatisfied(),
            Rule::class . ' did not report is was satisfied once'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenFlipNotFound()
    {
        $rule = new Rule(
            $this->flipService,
            'foo-bar-flip'
        );

        $this->flipService->shouldReceive('fetchFlipById')
            ->with('foo-bar-flip')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertFalse(
            $rule->isSatisfiedBy(new BasicRuleItem()),
            Rule::class . ' is satisfied when flip is not registered'
        );

        $this->assertEquals(
            0,
            $rule->timesSatisfied(),
            Rule::class . ' reported it was satisfied'
        );
    }
}
