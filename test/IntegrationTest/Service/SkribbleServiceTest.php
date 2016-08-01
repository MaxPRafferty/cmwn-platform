<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use Skribble\Service\SkribbleServiceInterface;
use Skribble\Skribble;
use Skribble\SkribbleInterface;
use Zend\Paginator\Paginator;

/**
 * Test SkribbleServiceTest
 *
 * @group Skribble
 * @group Db
 * @group IntegrationTest
 * @group SkribbleService
 * @group SkribbleRule
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleServiceTest extends TestCase
{
    /**
     * @var SkribbleServiceInterface
     */
    protected $skribbleService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../DataSets/skribble.dataset.php';

        return new ArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpSkribbleService()
    {
        $this->skribbleService = TestHelper::getServiceManager()->get(SkribbleServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllForUser()
    {
        $page   = new Paginator($this->skribbleService->fetchAllForUser('english_student'));
        $actual = [];
        foreach ($page->getIterator() as $skribble) {
            /** @var SkribbleInterface $skribble */
            $this->assertInstanceOf(
                SkribbleInterface::class,
                $skribble,
                'Skribble Service will not return back skribbles'
            );

            array_push($actual, $skribble->getArrayCopy());
        }

        $expected = [
            [
                'skribble_id' => 'baz-bat',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'COMPLETE',
                'created_by'  => 'english_student',
                'friend_to'   => 'math_student',
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
            [
                'skribble_id' => 'foo-bar',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'NOT_COMPLETE',
                'created_by'  => 'english_student',
                'friend_to'   => null,
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
            [
                'skribble_id' => 'qux-thud',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'PROCESSING',
                'created_by'  => 'english_student',
                'friend_to'   => 'math_student',
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Incorrect Skribbles for english_student Returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllDraftsForUser()
    {

        $page   = new Paginator($this->skribbleService->fetchDraftForUser('english_student'));
        $actual = [];
        foreach ($page->getIterator() as $skribble) {
            /** @var SkribbleInterface $skribble */
            $this->assertInstanceOf(
                SkribbleInterface::class,
                $skribble,
                'Skribble Service will not return back skribbles'
            );

            array_push($actual, $skribble->getArrayCopy());
        }

        $expected = [
            [
                'skribble_id' => 'foo-bar',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'NOT_COMPLETE',
                'created_by'  => 'english_student',
                'friend_to'   => null,
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Incorrect Draft Skribbles Returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSentForUser()
    {

        $page   = new Paginator($this->skribbleService->fetchSentForUser('english_student'));
        $actual = [];
        foreach ($page->getIterator() as $skribble) {
            /** @var SkribbleInterface $skribble */
            $this->assertInstanceOf(
                SkribbleInterface::class,
                $skribble,
                'Skribble Service will not return back skribbles'
            );

            array_push($actual, $skribble->getArrayCopy());
        }

        $expected = [
            [
                'skribble_id' => 'baz-bat',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'COMPLETE',
                'created_by'  => 'english_student',
                'friend_to'   => 'math_student',
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
            [
                'skribble_id' => 'qux-thud',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'PROCESSING',
                'created_by'  => 'english_student',
                'friend_to'   => 'math_student',
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Incorrect Sent Skribbles Returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllReceivedForUser()
    {

        $page   = new Paginator($this->skribbleService->fetchReceivedForUser('math_student'));
        $actual = [];
        foreach ($page->getIterator() as $skribble) {
            /** @var SkribbleInterface $skribble */
            $this->assertInstanceOf(
                SkribbleInterface::class,
                $skribble,
                'Skribble Service will not return back skribbles'
            );

            array_push($actual, $skribble->getArrayCopy());
        }

        $expected = [
            [
                'skribble_id' => 'baz-bat',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'COMPLETE',
                'created_by'  => 'english_student',
                'friend_to'   => 'math_student',
                'read'        => false,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
            [
                'skribble_id' => 'fizz-buzz',
                'version'     => '1',
                'url'         => 'https://media.changemyworldnow.com/f/abcdef',
                'created'     => '2016-04-27 10:48:44',
                'updated'     => '2016-04-27 10:48:46',
                'deleted'     => null,
                'status'      => 'COMPLETE',
                'created_by'  => 'manchuck',
                'friend_to'   => 'math_student',
                'read'        => true,
                'rules'       => [
                    'background' => null,
                    'effect'     => null,
                    'sound'      => null,
                    'items'      => [],
                    'messages'   => [],
                ],
            ],
        ];

        $this->assertEquals(
            $expected,
            $actual,
            'Incorrect Received ®Skribbles Returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchSkribble()
    {
        $skribble = $this->skribbleService->fetchSkribble('foo-bar');
        $expected = [
            'skribble_id' => 'foo-bar',
            'version'     => '1',
            'url'         => 'https://media.changemyworldnow.com/f/abcdef',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,
            'status'      => 'NOT_COMPLETE',
            'created_by'  => 'english_student',
            'friend_to'   => null,
            'read'        => false,
            'rules'       => [
                'background' => null,
                'effect'     => null,
                'sound'      => null,
                'items'      => [],
                'messages'   => [],
            ],
        ];

        $this->assertEquals(
            $expected,
            $skribble->getArrayCopy(),
            'Incorrect Skribble Returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribble()
    {
        $change = new Skribble([
            'skribble_id' => 'foo-bar',
            'version'     => '1',
            'url'         => 'https://media.changemyworldnow.com/f/abcdef',
            'created'     => '2016-04-27 10:48:44',
            'updated'     => '2016-04-27 10:48:46',
            'deleted'     => null,
            'status'      => 'COMPLETE',
            'created_by'  => 'english_student',
            'friend_to'   => null,
            'read'        => false,
            'rules'       => [
                'background' => null,
                'effect'     => null,
                'sound'      => null,
                'items'      => [],
                'messages'   => [],
            ],
        ]);

        $this->skribbleService->updateSkribble($change);
        $updated = $this->skribbleService->fetchSkribble('foo-bar');

        // Only check the changes since the service will update the date
        $this->assertEquals(
            $change->getStatus(),
            $updated->getStatus(),
            'Skribble was not updated'
        );
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteSkribble()
    {
        $this->skribbleService->deleteSkribble('foo-bar');
        $this->setExpectedException(NotFoundException::class);
        $this->skribbleService->fetchSkribble('foo-bar');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenSkribbleNotFound()
    {
        $this->setExpectedException(NotFoundException::class);
        $this->skribbleService->fetchSkribble('not-real');
    }
}
