<?php

namespace RuleTest\Action\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\CallbackAction;
use Rule\Action\Collection\ActionCollection;
use Rule\Action\NoopAction;
use Rule\Action\Service\BuildActionCollectionFactory;
use Rule\Action\Service\ActionManager;
use Rule\Action\Service\ActionManagerFactory;
use Rule\Action\Service\BuildActionFactory;
use Rule\Action\Service\BuildActionFromConfigFactory;
use Rule\Item\BasicRuleItem;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Test ActionManagerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActionManagerTest extends TestCase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var ActionManager
     */
    protected $manager;

    /**
     * @var ServiceManager
     */
    protected $container;

    /**
     * @before
     */
    public function setUpConfig()
    {
        $this->config = [
            'actions'                  => [
                'services'  => [
                    NoopAction::class => new NoopAction(),
                ],
                'factories' => [
                    NoopAction::class       => InvokableFactory::class,
                    CallbackAction::class   => BuildActionFromConfigFactory::class,
                ],
                'shared'    => [
                    NoopAction::class       => true,
                ],
                'abstract_factories' => [
                    BuildActionFromConfigFactory::class => BuildActionFromConfigFactory::class,
                ],
            ],
            'service_manager'          => [
                'aliases'   => [
                    'config' => 'Config',
                ],
                'factories' => [
                    ActionManager::class => ActionManagerFactory::class,
                ],
            ],
        ];
    }

    /**
     * @before
     */
    public function setUpContainer()
    {
        $this->container = new ServiceManager($this->config['service_manager']);
        $this->container->setService('Config', $this->config);
    }

    /**
     * @before
     */
    public function setUpManager()
    {
        $this->manager = $this->container->get(ActionManager::class);
    }

    /**
     * @test
     */
    public function testItShouldGetActions()
    {
        $this->assertInstanceOf(
            NoopAction::class,
            $this->manager->get(NoopAction::class),
            'Action Manager did not get a registered service'
        );

        $this->assertInstanceOf(
            NoopAction::class,
            $this->manager->build(NoopAction::class),
            'Action Manager did not build a registered service'
        );
    }

    /**
     * @test
     */
    public function testItShouldGetActionBasedOnRequestedName()
    {
        $called         = false;
        $callAbleAction = function () use (&$called) {
            $called = true;
        };

        $this->config[BuildActionFromConfigFactory::class][CallbackAction::class] = [
            $callAbleAction,
        ];

        $container = new ServiceManager($this->config['service_manager']);
        $container->setService('Config', $this->config);

        $manager = $container->get(ActionManager::class);
        $action  = $manager->get(CallbackAction::class);

        $this->assertInstanceOf(
            CallbackAction::class,
            $action,
            'Action Manager did not build an action'
        );

        $action(new BasicRuleItem());
        $this->assertTrue(
            $called,
            'Action was not built with the parameters'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildActionBasedOnConfig()
    {
        $dependency = new ActionDependency();
        $this->config[BuildActionFromConfigFactory::class] = [
            'login-flip-action' => [
                'action_class' => ActionWithDependency::class,
                ActionDependency::class
            ],

            'another-flip-action' => [
                'action_class' => ActionWithDependency::class,
                ActionDependency::class
            ],
        ];

        $container = new ServiceManager($this->config['service_manager']);
        $container->setService('Config', $this->config);
        $container->setService(ActionDependency::class, $dependency);

        /** @var ActionManager $manager */
        $manager = $container->get(ActionManager::class);
        $action  = $manager->build('login-flip-action', ['foo' => 'bar']);

        $this->assertInstanceOf(
            ActionWithDependency::class,
            $action,
            'Action Manager did not build an action'
        );

        $this->assertSame(
            $dependency,
            $action->depend,
            'Config Action Factory did not inject correct dependency'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildActionBasedWithBuilder()
    {
        $called         = false;
        $callAbleAction = function () use (&$called) {
            $called = true;
        };

        $this->manager->setFactory('CustomAction', new BuildActionFactory());

        $action = $this->manager->build(
            'CustomAction',
            [
                'action_class' => CallbackAction::class,
                $callAbleAction,
            ]
        );

        $this->assertInstanceOf(
            CallbackAction::class,
            $action,
            'Action Manager did not build Custom Action action'
        );

        $action(new BasicRuleItem());
        $this->assertTrue(
            $called,
            'build Custom Action was not built with the parameters'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildActionCollectionBasedOnConfig()
    {
        $dependency = new ActionDependency();
        $this->config[BuildActionFromConfigFactory::class] = [
            'login-flip-action' => [
                'action_class' => ActionWithDependency::class,
                ActionDependency::class
            ],

            'another-flip-action' => [
                'action_class' => ActionWithDependency::class,
                ActionDependency::class
            ],
        ];

        $container = new ServiceManager($this->config['service_manager']);
        $container->setService('Config', $this->config);
        $container->setService(ActionDependency::class, $dependency);

        /** @var ActionManager $manager */
        $manager = $container->get(ActionManager::class);
        $manager->setFactory('MyCollection', new BuildActionCollectionFactory());

        /** @var ActionCollection $collection */
        $collection  = $manager->build(
            'MyCollection',
            [
                'action_collection_class' => ActionCollection::class,
                'login-flip-action',
                'another-flip-action',
                new NoopAction(),
                NoopAction::class
            ]
        );

        $this->assertInstanceOf(
            ActionCollection::class,
            $collection,
            'Action Manager did not build a collection of actions'
        );

        $iterator = $collection->getIterator();
        $iterator->rewind();
        $this->assertInstanceOf(
            ActionWithDependency::class,
            $iterator->current(),
            'Action Manager built the incorrect action in the collection for the 1st action'
        );

        $iterator->next();
        $this->assertInstanceOf(
            ActionWithDependency::class,
            $iterator->current(),
            'Action Manager built the incorrect action in the collection for the 2nd action'
        );

        $iterator->next();
        $this->assertInstanceOf(
            NoopAction::class,
            $iterator->current(),
            'Action Manager built the incorrect action in the collection for the 3rd action'
        );

        $iterator->next();
        $this->assertInstanceOf(
            NoopAction::class,
            $iterator->current(),
            'Action Manager built the incorrect action in the collection for the 4th action'
        );
    }
}
