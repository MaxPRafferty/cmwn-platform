<?php

namespace ApiTest\Rule\Action;

use Api\Links\GroupLink;
use Api\Rule\Action\AddTypeLinksAction;
use Api\V1\Rest\User\UserEntity;
use Application\Utils\StaticType;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Item\RuleItemInterface;
use Rule\Provider\BasicValueProvider;

/**
 * Class AddTypeLinksActionTest
 * @package ApiTest\Rule\Action
 */
class AddTypeLinksActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var RuleItemInterface
     */
    protected $item;

    /**
     * @before
     */
    public function setUpProviders()
    {
        $this->item = new BasicRuleItem();
    }

    /**
     * @test
     */
    public function testItShouldAddTypeLinks()
    {
        $entity = new UserEntity();
        $this->item->append(new BasicValueProvider('types', ['class', 'school']));
        $this->item->append(new BasicValueProvider('provider', $entity));

        StaticType::setTypes(['class' => 'group_class', 'school' => 'group_school']);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));

        $action = new AddTypeLinksAction(GroupLink::class, 'types', 'provider');
        $action($this->item);

        $this->assertTrue($entity->getLinks()->has('group_class'));
        $this->assertTrue($entity->getLinks()->has('group_school'));
    }

    /**
     * @test
     */
    public function testItShouldNotAddLinksIfEntityIsNotLinksAware()
    {
        $entity = new UserEntity();
        $this->item->append(new BasicValueProvider('provider', $entity->getArrayCopy()));
        StaticType::setTypes(['class' => 'group_class', 'school' => 'group_school']);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));

        $action = new AddTypeLinksAction(GroupLink::class, 'types', 'provider');
        $action($this->item);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));
    }
}
