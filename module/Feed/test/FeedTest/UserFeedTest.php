<?php

namespace FeedTest;

use Feed\UserFeed;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;

/**
 * Class FeedTest
 */
class UserFeedTest extends TestCase
{
    /**
     * @var array $data
     */
    protected $data;

    /**
     * @before
     */
    public function setUpData()
    {
        $this->data = [
            'feed_id'      => 'es_friend_feed',
            'sender'       => 'english_student',
            'title'        => 'Friendship Made',
            'message'      => 'became friends with',
            'priority'     => 5,
            'posted'       => '2016-04-15 11:49:08',
            'visibility'   => 2,
            'type'         => 'FRIEND',
            'type_version' => 1,
            'created'      => '2016-04-15 11:49:08',
            'updated'      => '2016-04-15 11:49:08',
            'deleted'      => null,
            'read_flag'    => 1,
        ];
    }

    /**
     * @test
     */
    public function testItShouldCreateFeedWithData()
    {
        $feed = new UserFeed($this->data);
        $this->assertEquals($feed->getArrayCopy(), $this->data);
    }

    /**
     * @test
     */
    public function testItShouldHydrateWithData()
    {
        $feed = new UserFeed();

        $feed->exchangeArray($this->data);

        $this->assertEquals($this->data, $feed->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $data = [
            'feed_id'      => null,
            'sender'       => null,
            'title'        => null,
            'message'      => null,
            'priority'     => null,
            'posted'       => null,
            'visibility'   => null,
            'type'         => null,
            'type_version' => null,
            'created'      => null,
            'updated'      => null,
            'deleted'      => null,
            'read_flag'    => null
        ];

        $feed = new UserFeed($data);
        $this->assertEquals($feed->getArrayCopy(), $data);
    }

    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithSenderAsUserObjects()
    {
        $data = $this->data;
        $data['sender'] = new Child(['user_id' => $this->data['sender']]);
        $feed = new UserFeed($data);
        $this->assertInstanceOf(Child::class, $feed->getSender());
    }
}
