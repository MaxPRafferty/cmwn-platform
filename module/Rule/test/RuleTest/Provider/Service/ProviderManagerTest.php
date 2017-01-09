<?php

namespace RuleTest\Provider\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Provider\BasicValueProvider;
use Rule\Provider\CallbackProvider;
use Rule\Provider\Collection\ProviderCollection;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\NoopProvider;
use Rule\Provider\Service\BuildProviderFactory;
use Rule\Provider\Service\BuildProviderCollectionFactory;
use Rule\Provider\Service\ProviderManager;
use Rule\Provider\Service\ProviderManagerFactory;
use Rule\Provider\Service\BuildProviderFromConfigFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Test ProviderManagerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProviderManagerTest extends TestCase
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var ProviderManager
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
            'providers'       => [
                'aliases'            => [
                    ProviderCollectionInterface::class => ProviderCollection::class,
                ],
                'factories'          => [
                    BasicValueProvider::class => BuildProviderFactory::class,
                    ProviderCollection::class => BuildProviderCollectionFactory::class,
                ],
                'abstract_factories' => [
                    BuildProviderFromConfigFactory::class => BuildProviderFromConfigFactory::class,
                ],
                'shared'             => [
                    ProviderCollection::class => false,
                    BasicValueProvider::class => false,
                ],
            ],
            'service_manager' => [
                'aliases'   => [
                    'config' => 'Config',
                ],
                'factories' => [
                    ProviderManager::class => ProviderManagerFactory::class,
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
        $this->manager = $this->container->get(ProviderManager::class);
    }

    /**
     * @test
     */
    public function testItShouldBuildProviderBasedOnConfig()
    {
        $dependency = new ProviderDependency();

        $this->config[BuildProviderFromConfigFactory::class] = [
            'foo-bar' => [
                'provider_class' => BasicValueProvider::class,
                'foo',
                'bar',
            ],

            'provider-with-dependency' => [
                'provider_class' => ProviderWithDependency::class,
                ProviderDependency::class,
            ],
        ];

        $container = new ServiceManager($this->config['service_manager']);
        $container->setService('Config', $this->config);
        $container->setService(ProviderDependency::class, $dependency);

        /** @var ProviderManager $manager */
        $manager  = $container->get(ProviderManager::class);
        $provider = $manager->build('foo-bar', ['baz' => 'bat']);

        $this->assertInstanceOf(
            BasicValueProvider::class,
            $provider,
            BuildProviderFromConfigFactory::class . ' did not build the correct Provider'
        );

        $this->assertEquals(
            'foo',
            $provider->getName(),
            BuildProviderFromConfigFactory::class . ' did not build correct name into provider'
        );

        $this->assertEquals(
            'bar',
            $provider->getValue(),
            BuildProviderFromConfigFactory::class . ' did not build correct value into provider'
        );

        $depProvider = $manager->build('provider-with-dependency');

        $this->assertInstanceOf(
            ProviderWithDependency::class,
            $depProvider,
            BuildProviderFromConfigFactory::class . ' did not build correct provider'
        );

        $this->assertSame(
            $dependency,
            $depProvider->depend,
            BuildProviderFromConfigFactory::class . ' did not inject the dependency into the provider'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildCollectionWithNoClassSpecified()
    {
        $provider        = new BasicValueProvider('foo', 'bar');
        $serviceProvider = new BasicValueProvider('baz', 'bat');
        $this->manager->setService('MyProvider', $serviceProvider);
        $this->manager->setFactory('MyCollection', new BuildProviderCollectionFactory());
        /** @var ProviderCollection $collection */
        $collection = $this->manager->build(
            'MyCollection',
            [
                'provider_collection_class' => ProviderCollection::class,
                'providers'                 => [
                    $provider,
                    'MyProvider',
                    'provider' => [
                        'name'    => BasicValueProvider::class,
                        'options' => ['fizz', 'buzz'],
                    ],
                ],
            ]
        );

        $this->assertInstanceOf(
            ProviderCollection::class,
            $collection,
            BuildProviderCollectionFactory::class . ' built the wrong Collection'
        );

        $iterator = $collection->getIterator();
        $iterator->rewind();
        $this->assertEquals(
            'foo',
            $iterator->key(),
            BuildProviderCollectionFactory::class . ' did not add the passed in provider'
        );

        $this->assertEquals(
            'bar',
            $iterator->current(),
            BuildProviderCollectionFactory::class . ' did not add the passed in provider'
        );

        $iterator->next();
        $this->assertEquals(
            'baz',
            $iterator->key(),
            BuildProviderCollectionFactory::class . ' did not get the provider from the container'
        );

        $this->assertEquals(
            'bat',
            $iterator->current(),
            BuildProviderCollectionFactory::class . ' did not get the provider from the container'
        );

        $iterator->next();

        $this->assertEquals(
            'fizz',
            $iterator->key(),
            BuildProviderCollectionFactory::class . ' did not pass the correct name when building a provider'
        );

        $this->assertEquals(
            'buzz',
            $iterator->current(),
            BuildProviderCollectionFactory::class . ' did not pass the correct value when building a provider'
        );
    }
}
