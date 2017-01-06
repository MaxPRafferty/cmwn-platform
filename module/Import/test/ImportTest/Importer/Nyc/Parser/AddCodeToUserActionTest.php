<?php

namespace ImportTest\Importer\Nyc\Parser;

use Application\Utils\Date\DateTimeFactory;
use Import\Importer\Nyc\Parser\AddCodeToUserAction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Test AddCodeToUserActionTest
 *
 * @group Action
 * @group Import
 * @group User
 * @group NycImport
 */
class AddCodeToUserActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Adult
     */
    protected $user;

    /**
     * @var \Mockery\MockInterface|\User\UserAwareInterface
     */
    protected $userAware;

    /**
     * @var \Mockery\MockInterface|\Security\Service\SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Adult();
        $this->user->setUserName('MANCHUCK');
    }

    /**
     * @before
     */
    public function setUpUserAware()
    {
        $this->userAware = \Mockery::mock('\User\UserAwareInterface');
        $this->userAware->shouldReceive('getUser')->andReturn($this->user)->byDefault();
    }

    /**
     * @before
     */
    public function setUpSecurityService()
    {
        $this->securityService = \Mockery::mock('\Security\Service\SecurityServiceInterface');
    }

    /**
     * @test
     */
    public function testItShouldReportCorrectAction()
    {
        $action = new AddCodeToUserAction($this->userAware, $this->securityService, 'foo_bar');
        $this->assertEquals(
            'Setting code to user "MANCHUCK"',
            $action,
            'AddCodeToUserAction is not reporting correct action'
        );
    }

    /**
     * @test
     */
    public function testItShouldSaveCodeToUser()
    {
        $codeStart = DateTimeFactory::factory('now');
        $this->securityService->shouldReceive('saveCodeToUser')
            ->once()
            ->with('foo_bar', $this->user, 30, $codeStart);

        $action = new AddCodeToUserAction($this->userAware, $this->securityService, 'foo_bar');
        $action->setCodeStart($codeStart);
        $this->assertEquals(2, $action->priority(), 'AddCodeToUserAction has wrong priority');
        $action->execute();
    }
}
