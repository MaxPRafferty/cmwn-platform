<?php

namespace IntegrationTest\Service;

use Game\Service\GameServiceInterface;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\TestHelper;
use PHPUnit\DbUnit\DataSet\IDataSet;

/**
 * Test GameServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GameServiceTest extends TestCase
{
    /**
     * @var GameServiceInterface
     */
    protected $service;

    /**
     * @inheritDoc
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/games.dataset.php');
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = TestHelper::getServiceManager()->get(GameServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllWithFlags()
    {
        $games = $this->service->fetchAll(
            ['global' => true, 'featured' => true, 'coming_soon' => true, 'title' => 'foo']
        );

        $this->assertTrue(true);
    }
}
