<?php

namespace RuleTest\Engine;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Action\CallbackAction;
use Rule\Action\Service\ActionManager;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Engine\Engine;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\SpecificationCollection;
use Rule\Rule\Service\RuleManager;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\ServiceManager;

/**
 * Test RulesEngineTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineTest extends TestCase
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
    public function setUpServiceManager()
    {
        $this->serviceManager = new ServiceManager($this->config['service_manager']);
        $this->serviceManager->setService('Config', $this->config);
    }

    /**
     * @before
     */
    public function setUpConfig()
    {
        $this->config = include __DIR__ . '/../../../config/module.config.php';
    }

    /**
     * @test
     */
    public function testItShouldAttachToEvents()
    {
        $called    = false;
        $spec      = [
            'id'      => 'foo-bar',
            'name'    => 'This is a test that the foo will bar',
            'when'    => 'some.event',
            'rules'   => [
                AlwaysSatisfiedRule::class,
            ],
            'actions' => [
                new CallbackAction(function () use (&$called) {
                    $called = true;
                }),
            ],
        ];

        $collection = new SpecificationCollection();
        $collection->append(new ArraySpecification($spec));

        $sharedEvents = new SharedEventManager();
        $events       = new EventManager($sharedEvents);
        new Engine(
            $sharedEvents,
            $this->serviceManager->get(ActionManager::class),
            $this->serviceManager->get(RuleManager::class),
            $this->serviceManager->get(ProviderManager::class),
            $collection
        );

        $event = new Event();
        $event->setName('some.event');
        $events->triggerEvent($event);

        $this->assertTrue(
            $called,
            'It appears that the Engine did nothing'
        );
    }

    /**
     * @test
     */
    public function testItShouldPerformWell()
    {
        $expectedTimes = 1000;
        $calledTimes   = 0;
        $collection    = new SpecificationCollection();
        foreach (range(1, $expectedTimes) as $specCount) {
            $collection->append(new ArraySpecification([
                'id'      => 'foo-bar-' . $specCount,
                'name'    => 'This is a test that the foo will bar ' . $specCount . ' time(s)',
                'when'    => 'some.event',
                'rules'   => [
                    AlwaysSatisfiedRule::class,
                ],
                'actions' => [
                    new CallbackAction(function () use (&$calledTimes) {
                        $calledTimes++;
                    }),
                ],
            ]));
        }

        $sharedEvents = new SharedEventManager();
        $events       = new EventManager($sharedEvents);
        new Engine(
            $sharedEvents,
            $this->serviceManager->get(ActionManager::class),
            $this->serviceManager->get(RuleManager::class),
            $this->serviceManager->get(ProviderManager::class),
            $collection
        );

        $event = new Event();
        $event->setName('some.event');
        $events->triggerEvent($event);

        $this->assertEquals(
            $expectedTimes,
            $calledTimes,
            'It appears that the Engine did nothing'
        );
    }
}
