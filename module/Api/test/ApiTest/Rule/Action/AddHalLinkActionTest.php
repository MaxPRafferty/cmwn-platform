<?php

namespace ApiTest\Rule\Action;

use Api\Links\UserLink;
use Api\Rule\Action\AddHalLinkAction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Rule\Item\BasicRuleItem;
use Rule\Item\RuleItemInterface;
use Rule\Provider\BasicValueProvider;
use ZF\Hal\Entity;

/**
 * Class AddHalLinkActionTest
 *
 * @package ApiTest\Rule\Action
 */
class AddHalLinkActionTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var RuleItemInterface
     */
    protected $item;

    /**
     * @before
     */
    public function setUpRule()
    {
        $this->item = new BasicRuleItem();
    }

    /**
     * @test
     */
    public function testItShouldAddHalLinksToEntity()
    {
        $entity = new Entity(['foo' => 'bar']);
        $action = new AddHalLinkAction(UserLink::class, 'provider');
        $this->item->append(new BasicValueProvider('provider', $entity));
        $this->assertFalse($entity->getLinks()->has('user'));
        $action($this->item);
        $this->assertTrue($entity->getLinks()->has('user'));
    }
}
