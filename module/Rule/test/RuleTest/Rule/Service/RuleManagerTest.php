<?php

namespace RuleTest\Rule\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\AndRule;
use Rule\Rule\Basic\EitherRule;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Service\DependantRuleFactory;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Rule\Basic\NotRule;
use Rule\Rule\Basic\NotRuleFactory;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Service\RuleCollectionFactory;
use Rule\Rule\Service\RuleManager;
use Rule\Rule\Service\RuleManagerFactory;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Test RuleManagerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleManagerTest extends TestCase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var RuleManager
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
            'rules'           => [
                'aliases'   => [
                    RuleCollectionInterface::class => RuleCollection::class,
                ],
                'services'  => [
                    AlwaysSatisfiedRule::class => new AlwaysSatisfiedRule(),
                    NeverSatisfiedRule::class  => new NeverSatisfiedRule(),
                    RuleCollection::class      => new RuleCollection(),
                ],
                'factories' => [
                    AlwaysSatisfiedRule::class => InvokableFactory::class,
                    NeverSatisfiedRule::class  => InvokableFactory::class,
                    AndRule::class             => DependantRuleFactory::class,
                    NotRule::class             => DependantRuleFactory::class,
                    EitherRule::class          => DependantRuleFactory::class,
                    RuleCollection::class      => RuleCollectionFactory::class,
                ],
                'shared'    => [
                    AlwaysSatisfiedRule::class => true,
                    NeverSatisfiedRule::class  => true,
                    RuleCollection::class      => false,
                    AndRule::class             => false,
                    NotRule::class             => false,
                    EitherRule::class          => false,
                ],
            ],
            'service_manager' => [
                'aliases'   => [
                    'config' => 'Config',
                ],
                'factories' => [
                    RuleManager::class => RuleManagerFactory::class,
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
        $this->manager = $this->container->get(RuleManager::class);
    }

    /**
     * @test
     */
    public function testItShouldGetRules()
    {
        $this->assertInstanceOf(
            AlwaysSatisfiedRule::class,
            $this->manager->get(AlwaysSatisfiedRule::class),
            'AlwaysSatisfiedRule was not returned from Rule Manager'
        );

        $this->assertInstanceOf(
            NeverSatisfiedRule::class,
            $this->manager->get(NeverSatisfiedRule::class),
            'AlwaysSatisfiedRule was not returned from Rule Manager'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildBasicRules()
    {
        $this->assertInstanceOf(
            NotRule::class,
            $this->manager->build(NotRule::class, [new NeverSatisfiedRule()]),
            'EitherRule was not returned from Rule Manager'
        );

        $this->assertInstanceOf(
            AlwaysSatisfiedRule::class,
            $this->manager->build(AlwaysSatisfiedRule::class, []),
            'AlwaysSatisfiedRule was not returned from Rule Manager'
        );

        $this->assertInstanceOf(
            NeverSatisfiedRule::class,
            $this->manager->build(NeverSatisfiedRule::class, []),
            'AlwaysSatisfiedRule was not returned from Rule Manager'
        );

        $this->assertInstanceOf(
            AndRule::class,
            $this->manager->build(AndRule::class, []),
            'AndRule was not returned from Rule Manager'
        );

        $this->assertInstanceOf(
            EitherRule::class,
            $this->manager->build(EitherRule::class, []),
            'EitherRule was not returned from Rule Manager'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildComplexRules()
    {
        /** @var AndRule $andRule */
        $andRule = $this->manager->build(
            AndRule::class,
            [
                'rule_class' => EitherRule::class,
                'rules'      => [
                    ['rule' => new AlwaysSatisfiedRule()],
                    ['rule' => AlwaysSatisfiedRule::class],
                    [
                        'rule' => [
                            'name'    => NotRule::class,
                            'options' => [new NeverSatisfiedRule()],
                        ],
                    ],
                    [
                        'rule'     => [
                            'name'    => NotRule::class,
                            'options' => [NeverSatisfiedRule::class],
                        ],
                        'operator' => 'or',
                        'or_group' => 'foo-bar',
                    ],
                ],
            ]
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy(new BasicRuleItem())
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildCollection()
    {
        $actualCollection = $this->manager->build(
            RuleCollection::class,
            [
                'rules' => [
                    // Adds not rule
                    [
                        'rule'     => [
                            'name' => NeverSatisfiedRule::class,
                        ],
                        'operator' => 'not',
                    ],

                    // Adds rule instance
                    ['rule' => new AlwaysSatisfiedRule()],

                    // Builds the rule
                    ['rule' => AlwaysSatisfiedRule::class],

                    // Build rule without rule key
                    AlwaysSatisfiedRule::class,

                    // Or Rules
                    [
                        'rule'     => [
                            'name' => NeverSatisfiedRule::class,
                        ],
                        'operator' => 'or',
                        'or_group' => 'foo-bar',
                    ],
                    [
                        'rule'     => [
                            'name' => AlwaysSatisfiedRule::class,
                        ],
                        'operator' => 'or',
                        'or_group' => 'foo-bar',
                    ],
                    [
                        'rule'     => [
                            'name'    => NotRule::class,
                            'options' => [NeverSatisfiedRule::class],
                        ],
                        'operator' => 'or',
                        'or_group' => 'foo-bar',
                    ],
                ],
            ]
        );

        $expectedCollection = new RuleCollection();
        $expectedCollection->append(new NeverSatisfiedRule(), 'not');
        $expectedCollection->append(new AlwaysSatisfiedRule());
        $expectedCollection->append(new AlwaysSatisfiedRule());
        $expectedCollection->append(new AlwaysSatisfiedRule());

        $expectedCollection->append(new NeverSatisfiedRule(), 'or', 'foo-bar');
        $expectedCollection->append(new AlwaysSatisfiedRule(), 'or', 'foo-bar');
        $expectedCollection->append(new NotRule(new NeverSatisfiedRule()), 'or', 'foo-bar');

        $this->assertEquals(
            $expectedCollection,
            $actualCollection
        );

        $this->assertTrue(
            $actualCollection->isSatisfiedBy(new BasicRuleItem())
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildReallyComplexRules()
    {
        $andRule = $this->manager->build(
            EitherRule::class,
            [
                'rules'      => [
                    ['rule' => new AlwaysSatisfiedRule()],
                    ['rule' => AlwaysSatisfiedRule::class],
                    [
                        'rule' => [
                            'name'    => NotRule::class,
                            'options' => [new NeverSatisfiedRule()],
                        ],
                    ],
                    [
                        'rule'     => [
                            'name'    => AndRule::class,
                            'options' => [
                                'rules' => [
                                    // Adds not rule
                                    [
                                        'rule'     => [
                                            'name' => NeverSatisfiedRule::class,
                                        ],
                                        'operator' => 'not',
                                    ],

                                    // Adds rule instance
                                    ['rule' => new AlwaysSatisfiedRule()],

                                    // Builds the rule
                                    ['rule' => AlwaysSatisfiedRule::class],

                                    // Build rule without rule key
                                    AlwaysSatisfiedRule::class,

                                    // Or Rules
                                    [
                                        'rule'     => [
                                            'name' => NeverSatisfiedRule::class,
                                        ],
                                        'operator' => 'or',
                                        'or_group' => 'foo-bar',
                                    ],
                                    [
                                        'rule'     => [
                                            'name' => AlwaysSatisfiedRule::class,
                                        ],
                                        'operator' => 'or',
                                        'or_group' => 'foo-bar',
                                    ],
                                    [
                                        'rule'     => [
                                            'name'    => NotRule::class,
                                            'options' => [NeverSatisfiedRule::class],
                                        ],
                                        'operator' => 'or',
                                        'or_group' => 'foo-bar',
                                    ],
                                ],
                            ],
                        ],
                        'operator' => 'or',
                        'or_group' => 'foo-bar',
                    ],
                ],
            ]
        );

        $this->assertTrue(
            $andRule->isSatisfiedBy(new BasicRuleItem())
        );
    }
}
