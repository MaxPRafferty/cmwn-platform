<?php

namespace RuleTest\Engine\Service;

use PHPUnit\Framework\TestCase;
use Rule\Action\NoopAction;
use Rule\Action\Service\ActionManager;
use Rule\Action\Service\ActionManagerFactory;
use Rule\Engine\Service\BuildSpecificationCollectionFactory;
use Rule\Engine\Service\BuildSpecificationFromConfigFactory;
use Rule\Engine\Service\SpecificationCollectionFactory;
use Rule\Engine\Service\SpecificationManager;
use Rule\Engine\Service\SpecificationManagerFactory;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\EngineSpecification;
use Rule\Engine\Specification\SpecificationCollection;
use Rule\Engine\Specification\SpecificationCollectionInterface;
use Rule\Provider\Service\ProviderManager;
use Rule\Provider\Service\ProviderManagerFactory;
use Rule\Rule\Service\RuleManager;
use Rule\Rule\Service\RuleManagerFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Test SpecificationManagerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SpecificationManagerTest extends TestCase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var SpecificationManager
     */
    protected $manager;

    /**
     * @var ServiceManager
     */
    protected $container;

    /**
     * @before
     */
    public function setUpManager()
    {
        $this->manager = $this->container->get(SpecificationManager::class);
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
    public function setUpConfig()
    {
        $this->config = [
            'specifications' => [
                'aliases'            => [
                    SpecificationCollectionInterface::class => SpecificationCollection::class,
                ],
                'factories'          => [
                    ArraySpecification::class      => BuildSpecificationFromConfigFactory::class,
                    SpecificationCollection::class => BuildSpecificationCollectionFactory::class,
                    'AllSpecifications'            => SpecificationCollectionFactory::class,
                ],
                'shared'             => [
                    ArraySpecification::class      => false,
                    SpecificationCollection::class => false,
                ],
                'abstract_factories' => [
                    BuildSpecificationFromConfigFactory::class => BuildSpecificationFromConfigFactory::class,
                ],
            ],

            'service_manager' => [
                'aliases'   => [
                    'config'               => 'Config',
                    'ActionManager'        => ActionManager::class,
                    'ProviderManager'      => ProviderManager::class,
                    'RuleManager'          => RuleManager::class,
                    'SpecificationManager' => SpecificationManager::class,
                ],
                'factories' => [
                    ActionManager::class        => ActionManagerFactory::class,
                    ProviderManager::class      => ProviderManagerFactory::class,
                    RuleManager::class          => RuleManagerFactory::class,
                    SpecificationManager::class => SpecificationManagerFactory::class,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function testItShouldBuildFullSpecificationCollectionBaseOnConfig()
    {
        $expectedTimes = 1000;
        foreach (range(1, $expectedTimes) as $specCount) {
            $this->config[BuildSpecificationFromConfigFactory::class]['foo-bar-' . $specCount] = [
                'specification_class' => EngineSpecification::class,
                'id'                  => 'foo-bar-' . $specCount,
                'name'                => 'This is a test that the foo will bar ' . $specCount . ' time(s)',
                'when'                => 'some.event',
                'rules'               => [
                    AlwaysSatisfiedRule::class,
                ],
                'actions'             => [
                    new NoopAction(),
                ],
            ];
        }

        $container = new ServiceManager($this->config['service_manager']);
        $container->setService('Config', $this->config);

        /** @var ActionManager $manager */
        $manager            = $container->get(SpecificationManager::class);
        $fullSpecCollection = $manager->get('AllSpecifications');

        $this->assertInstanceOf(
            SpecificationCollection::class,
            $fullSpecCollection,
            SpecificationManager::class . ' was not able to build all the specifications'
        );
    }
}
