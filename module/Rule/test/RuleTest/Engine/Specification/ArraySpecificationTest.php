<?php

namespace RuleTest\Engine\Specification;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\NoopAction;
use Rule\Provider\BasicValueProvider;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Rule\Collection\RuleCollectionInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Test ArraySpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArraySpecificationTest extends TestCase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @before
     */
    public function setUpConfig()
    {
        $this->config = include __DIR__ . '/../../../../config/module.config.php';
    }

    /**
     * @before
     */
    public function setUpServiceManager()
    {
        $this->serviceManager = new ServiceManager($this->config['service_manager']);
        $this->serviceManager->setService('Config', $this->config);
    }

    /**
     * @test
     */
    public function testItShouldCreateRulesFromSpec()
    {
        $spec = [
            'id'        => 'foo-bar',
            'name'      => 'This is a test that the foo will bar',
            'when'      => ['some.event'],
            'rules'     => [
                AlwaysSatisfiedRule::class,
            ],
            'actions'   => [
                NoopAction::class,
            ],
            'providers' => [
                new BasicValueProvider('active_user', 'MANCHUCK'),
            ],
        ];

        $arraySpec = new ArraySpecification($spec);

        $this->assertEquals(
            $spec['id'],
            $arraySpec->getId(),
            'The Id does not match the expected spec'
        );

        $this->assertEquals(
            $spec['name'],
            $arraySpec->getName(),
            'The name does not match the expected spec'
        );

        $this->assertEquals(
            $spec['when'],
            $arraySpec->getEventName(),
            'The Event name does not match the expected spec'
        );

        $item = $arraySpec->buildProvider($this->serviceManager->get('ProviderManager'));
        $this->assertEquals(
            'MANCHUCK',
            $item->getParam('active_user'),
            'The item that was built does not have the correct parameter'
        );

        $rules = $arraySpec->getRules($this->serviceManager->get('RuleManager'));
        $this->assertInstanceOf(
            RuleCollectionInterface::class,
            $rules,
            'Rules built are not a Collection of rules'
        );

        $this->assertTrue(
            $rules->isSatisfiedBy($item),
            'Spec for this array should be satisfied'
        );

        $actions = $arraySpec->getActions($this->serviceManager->get('ActionManager'));
        $this->assertInstanceOf(
            ActionCollectionInterface::class,
            $actions,
            'Actions built are not a Collection of actions'
        );
    }
}
