<?php

namespace ApiTest\V1\Rest\Import;

use Api\V1\Rest\Import\ImportEntity;
use Api\V1\Rest\Import\ImportResource;
use Application\Utils\Date\DateTimeFactory;
use Group\Group;
use Group\GroupAwareInterface;
use Group\Service\GroupServiceInterface;
use Import\ImporterInterface;
use Job\Service\JobServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Notice\NotificationAwareInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Zend\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\ResourceEvent;

/**
 * Test ImportResourceTest
 *
 * @group Api
 * @group Import
 * @group Group
 * @group Authentication
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportResourceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var \Mockery\MockInterface|JobServiceServiceInterface
     */
    protected $jobService;

    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var ImportResource
     */
    protected $resource;

    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * @var ResourceEvent
     */
    protected $event;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var \Mockery\MockInterface|GroupServiceInterface
     */
    protected $groupService;

    /**
     * @return array
     */
    public function getInputSpecification()
    {
        $moduleConfig = include __DIR__ . '/../../../../../config/module.config.php';
        return $moduleConfig['input_filter_specs']['Api\\V1\\Rest\\Import\\Validator'];
    }

    /**
     * @before
     */
    public function setUpAuthenticationService()
    {
        $this->authService = \Mockery::mock(AuthenticationServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpJobService()
    {
        $this->jobService = \Mockery::mock(JobServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->group = new Group(['group_id' => 'foo-bar']);
        $this->groupService = \Mockery::mock(GroupServiceInterface::class);
        $this->groupService->shouldReceive('fetchGroup')
            ->with('foo-bar')
            ->andReturn($this->group)
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->services = new ServiceManager();
        $this->services->setService(JobServiceInterface::class, $this->jobService);
        $this->services->setService(AuthenticationServiceInterface::class, $this->authService);
        $this->services->setService(GroupServiceInterface::class, $this->groupService);
    }

    /**
     * @before
     */
    public function setUpEvent()
    {
        $this->event = new ResourceEvent();
    }

    /**
     * @before
     */
    public function setUpInputFilter()
    {
        $factory           = new Factory();
        $this->inputFilter = $factory->createInputFilter($this->getInputSpecification());
        $this->event->setInputFilter($this->inputFilter);
    }

    /**
     * @before
     */
    public function setUpResource()
    {
        $this->resource = new ImportResource($this->services);
    }

    /**
     * @test
     */
    public function testItShouldSendBasicJobWithNoStartDate()
    {
        /** @var \Mockery\MockInterface|ImporterInterface $job */
        $job  = \Mockery::mock(ImporterInterface::class);
        $data = [
            'type'         => 'Nyc\DoeImporter',
            'student_code' => 'manchuck82',
            'teacher_code' => 'chuckman28',
            'file'         => 'NULL'
        ];

        $this->event->setName('create');
        $this->event->setParam('data', $data);

        $this->inputFilter->setData($data);

        $this->services->setService('Nyc\DoeImporter', $job);

        $job->shouldReceive('exchangeArray')
            ->with($this->inputFilter->getValues())
            ->once();

        $job->shouldNotReceive('setGroup');
        $job->shouldNotReceive('setEmail');

        $this->jobService->shouldReceive('sendJob')->with($job)->andReturn('foobar');

        $result = $this->resource->dispatch($this->event);
        $this->assertEquals(
            new ImportEntity('foobar'),
            $result,
            'Import Resource did not send job correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldSendBasicJobWithAGroup()
    {
        /** @var \Mockery\MockInterface|ImporterInterface $job */
        $job  = \Mockery::mock(ImporterInterface::class, GroupAwareInterface::class);
        $data = [
            'type'         => 'Nyc\DoeImporter',
            'student_code' => 'manchuck82',
            'teacher_code' => 'chuckman28',
            'code_start'   => DateTimeFactory::factory('tomorrow'),
            'file'         => 'NULL'
        ];

        $this->event->setName('create');
        $this->event->setParam('data', $data);

        $this->inputFilter->setData($data);
        $this->services->setService('Nyc\DoeImporter', $job);

        $job->shouldReceive('exchangeArray')
            ->with($data)
            ->once();

        $this->event->setRouteMatch(new RouteMatch(['group_id' => 'foo-bar']));
        $job->shouldReceive('setGroup')->with($this->group)->once();
        $job->shouldNotReceive('setEmail');

        $this->jobService->shouldReceive('sendJob')->with($job)->andReturn('foobar');

        $result = $this->resource->dispatch($this->event);
        $this->assertEquals(
            new ImportEntity('foobar'),
            $result,
            'Import Resource did not send job correctly'
        );
    }
    /**
     * @test
     */
    public function testItShouldSendBasicJobWithAFutureStartDateAndNotifyUser()
    {
        $user = new Adult();
        $user->setEmail('chuck@manchuck.com');
        /** @var \Mockery\MockInterface|ImporterInterface $job */
        $job  = \Mockery::mock(
            ImporterInterface::class,
            NotificationAwareInterface::class
        );
        $data = [
            'type'         => 'Nyc\DoeImporter',
            'student_code' => 'manchuck82',
            'teacher_code' => 'chuckman28',
            'code_start'   => DateTimeFactory::factory('today'),
            'file'         => 'NULL'
        ];

        $this->authService->shouldReceive('hasIdentity')
            ->andReturn(true);

        $this->event->setName('create');
        $this->event->setParam('data', $data);

        $this->inputFilter->setData($data);
        $this->services->setService('Nyc\DoeImporter', $job);

        $job->shouldReceive('exchangeArray')
            ->with($data)
            ->once();

        $job->shouldNotReceive('setGroup');
        $job->shouldReceive('setEmail')
            ->with('chuck@manchuck.com');

        $this->authService->shouldReceive('getIdentity')->andReturn($user);

        $this->jobService->shouldReceive('sendJob')->with($job)->andReturn('foobar');

        $result = $this->resource->dispatch($this->event);
        $this->assertEquals(
            new ImportEntity('foobar'),
            $result,
            'Import Resource did not send job correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnProblemWhenJobIsNotInServices()
    {
        $this->event->setName('create');
        $this->event->setParam('data', ['type' => 'foobar']);
        $this->inputFilter->setData(['type' => 'foobar']);
        $result = $this->resource->dispatch($this->event);

        $this->assertEquals(
            new ApiProblem(500, 'Invalid importer type'),
            $result,
            'Import Resource did not return back 500 error when missing job type'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnProblemWhenJobIsNotAnImportJob()
    {
        $this->services->setService('foobar', new \stdClass());
        $this->inputFilter->setData(['type' => 'foobar']);
        $this->event->setName('create');
        $this->event->setParam('data', ['type' => 'foobar']);
        $result = $this->resource->dispatch($this->event);

        $this->assertEquals(
            new ApiProblem(500, 'Not a valid importer'),
            $result,
            'Import Resource did not return back 500 error when missing job type'
        );
    }
}
