<?php

namespace SuggestTest\Filter;

use Group\Group;
use Group\Service\UserGroupServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Suggest\Filter\ClassFilter;
use Suggest\Filter\FilterCollection;
use Suggest\InvalidFilterException;
use Suggest\SuggestionCollection;
use User\Adult;
use User\Child;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\ServiceManager\ServiceManager;

/**
 * Test FilterCollectionTest
 *
 * @group Suggest
 * @group Filter
 * @group User
 * @group Group
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FilterCollectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ServiceManager
     */
    protected $service;

    /**
     * @var array
     */
    protected $filterConfig = [
        'class-filter' => \Suggest\Filter\ClassFilter::class,
    ];

    /**
     * @var \Mockery\MockInterface|UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var ClassFilter
     */
    protected $classFilter;

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->service = new ServiceManager();
        $this->service->setService(ClassFilter::class, $this->classFilter);
    }

    /**
     * @before
     */
    public function setUpClassFilter()
    {
        $this->classFilter = new ClassFilter($this->userGroupService);
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->userGroupService = \Mockery::mock(UserGroupServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldReturnUsers()
    {
        $student    = new Child(['user_id' => 'english_student']);
        $teacher    = new Adult(['user_id' => 'english_teacher']);
        $group      = new Group();
        $paginator  = new ArrayAdapter([$student, $teacher]);
        $collection = new SuggestionCollection();

        $this->userGroupService->shouldReceive("fetchGroupsForUser")
            ->andReturn(new ArrayAdapter([$group]))
            ->once();

        $this->userGroupService->shouldReceive('fetchUsersForGroup')
            ->andReturn($paginator)
            ->once();

        $filterCollection = new FilterCollection($this->service, $this->filterConfig);
        $filterCollection->getSuggestions($collection, $student);
        $this->assertEquals(
            ['english_student' => $student, 'english_teacher' => $teacher],
            $collection->getArrayCopy(),
            'Incorrect suggestions returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMissingFilterFromService()
    {
        $services   = new ServiceManager();
        $config     = ['foo-bar' => 'foobar'];
        $rules      = new FilterCollection($services, $config);
        $collection = new SuggestionCollection();
        $this->expectException(InvalidFilterException::class);
        $this->expectExceptionMessage('Missing filter: "foobar" from services');

        $rules->getSuggestions($collection, new Child());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWithInvalidFilter()
    {
        $services = new ServiceManager();
        $config   = ['foo-bar' => 'foobar'];
        $services->setService('foobar', new \stdClass());
        $rules      = new FilterCollection($services, $config);
        $collection = new SuggestionCollection();
        $this->expectException(InvalidFilterException::class);
        $this->expectExceptionMessage('Invalid Filter Provided');

        $this->assertEmpty($rules->getSuggestions($collection, new Child()));
    }

    /**
     * @test
     */
    public function testItShouldNotCreateFiltersTwice()
    {
        $filter = \Mockery::mock(ClassFilter::class);
        $filter->shouldReceive('getSuggestions')->byDefault();
        $services = \Mockery::mock(ServiceManager::class);
        $services->shouldReceive('has')->andReturn(true)->byDefault();
        $services->shouldReceive('get')
            ->with('foobar')
            ->once()
            ->andReturn($filter);

        $config     = ['foo-bar' => 'foobar'];
        $rules      = new FilterCollection($services, $config);
        $collection = new SuggestionCollection();

        $this->assertEmpty($rules->getSuggestions($collection, new Child()));
        $this->assertEmpty($rules->getSuggestions($collection, new Child()));
    }
}
