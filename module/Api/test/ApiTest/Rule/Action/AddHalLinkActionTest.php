<?php

namespace ApiTest\Rule\Action;

use Api\Links\UserLink;
use Api\Rule\Action\AddHalLinkAction;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Rule\Item\BasicRuleItem;
use Rule\Item\RuleItemInterface;
use Rule\Provider\BasicValueProvider;

/**
 * Class AddHalLinkActionTest
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
        $entity = new UserEntity();
        $this->item->append(new BasicValueProvider('provider', $entity));
        $action = new AddHalLinkAction(UserLink::class, 'provider');
        $this->assertFalse($entity->getLinks()->has('user'));
        $action($this->item);
        $this->assertTrue($entity->getLinks()->has('user'));
    }

    /**
     * @test
     */
    public function testItShouldNotAddHalLinksIfEntityIsNotLinksAware()
    {
        $entity = new UserEntity();
        $this->item->append(new BasicValueProvider('provider', $entity->getArrayCopy()));
        $action = new AddHalLinkAction(UserLink::class, 'provider');
        $this->assertFalse($entity->getLinks()->has('user'));
        $action($this->item);
        $this->assertFalse($entity->getLinks()->has('user'));
    }
}
