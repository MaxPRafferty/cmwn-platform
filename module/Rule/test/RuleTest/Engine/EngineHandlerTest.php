<?php

namespace RuleTest\Engine;

use PHPUnit\Framework\TestCase;
use Rule\Action\CallbackAction;
use Rule\Action\Service\ActionManager;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\EngineHandler;
use Rule\Rule\Service\RuleManager;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceManager;

/**
 * Test EngineHandlerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineHandlerTest extends TestCase
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
    public function testItShouldExecuteActionsWhenRulesAreHappy()
    {

        $spec = [
            'id'      => 'foo-bar',
            'name'    => 'This is a test that the foo will bar',
            'when'    => 'some.event',
            'rules'   => [
                new AlwaysSatisfiedRule(),
            ],
            'actions' => [
                new CallbackAction(function () use (&$called) {
                    $called = true;
                }),
            ],
        ];

        $arraySpec = new ArraySpecification($spec);
        $handler   = new EngineHandler(
            $this->serviceManager->get(ActionManager::class),
            $this->serviceManager->get(RuleManager::class),
            $this->serviceManager->get(ProviderManager::class)
        );

        $handler->setSpecification($arraySpec);
        $handler(new Event('some.event'));

        $this->assertTrue(
            $called,
            'It appears that the Engine Handler did nothing'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExecuteActionsWhenRulesAreSad()
    {
        $called = false;
        $spec   = [
            'id'      => 'foo-bar',
            'name'    => 'This is a test that the foo will never bar',
            'when'    => 'some.event',
            'rules'   => [
                NeverSatisfiedRule::class,
            ],
            'actions' => [
                new CallbackAction(function () use (&$called) {
                    $called = true;
                }),
            ],
        ];

        $arraySpec = new ArraySpecification($spec);
        $handler   = new EngineHandler(
            $this->serviceManager->get(ActionManager::class),
            $this->serviceManager->get(RuleManager::class),
            $this->serviceManager->get(ProviderManager::class)
        );

        $handler->setSpecification($arraySpec);
        $handler(new Event('some.event'));

        $this->assertFalse(
            $called,
            'It appears that the Engine Handler executed actions when it was\'t supposed too'
        );
    }
}
