<?php


namespace ApiTest\Rule\Action;

use Api\Rule\Action\ThrowException;
use Application\Exception\NotAuthorizedException;
use Application\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Rule\Item\BasicRuleItem;

/**
 * unit test for throw exception action
 */
class ThrowExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMessageAndCodeAreGiven()
    {
        $action = new ThrowException(NotFoundException::class, 'entity not found', 404);
        try {
            $action(new BasicRuleItem());
            $this->fail("it did not throw exception");
        } catch (NotFoundException $nf) {
            $this->assertEquals('entity not found', $nf->getMessage());
            $this->assertEquals(404, $nf->getCode());
        }
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMessageAndCodeAreNotGiven()
    {
        $this->expectException(NotAuthorizedException::class);
        $action = new ThrowException(NotAuthorizedException::class);
        $action(new BasicRuleItem());
    }

    /**
     * @test
     */
    public function testItShouldThrowRuntimeExceptionWhenIllegalExceptionClassNameIsGiven()
    {
        $this->expectException(\RuntimeException::class);
        $action = new ThrowException("foo");
        $action(new BasicRuleItem());
    }
}
