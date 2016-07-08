<?php

namespace MediaTest\Service;

use Application\Exception\NotFoundException;
use IntegrationTest\StreamingTestAdapter as TestAdapter;
use Media\InvalidResponseException;
use Media\MediaCollection;
use Media\MediaInterface;
use Media\Service\MediaService;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Test MediaServiceTest
 *
 * @group Media
 * @group Http
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class MediaServiceTest extends TestCase
{
    /**
     * @var TestAdapter
     */
    protected $adapter;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * @before
     */
    public function setUpHttpClient()
    {
        $this->adapter = new TestAdapter();
        $this->client  = new HttpClient('http://www.google.com');
        $this->client->setAdapter($this->adapter);
    }

    /**
     * @before
     */
    public function setUpMediaService()
    {
        $this->mediaService = new MediaService($this->client);
    }

    /**
     * @after
     */
    public function tearDown()
    {
        @unlink(realpath(getcwd() . '/../tmp/70116569037'));
    }

    /**
     * @test
     */
    public function testItShouldImportData()
    {
        $this->adapter->setResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.import.response'))
        );

        $media = $this->mediaService->importMediaData('70116569037');

        $this->assertEquals(
            Request::fromString(file_get_contents(__DIR__ . '/_response/valid.import.request'))->toString(),
            $this->client->getRequest()->toString()
        );

        $this->assertInstanceOf(MediaInterface::class, $media);
        $expected = [
            'media_id'    => '70116569037',
            'asset_type'  => 'background',
            'check'       => [
                'type'  => 'sha1',
                'value' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            ],
            'mime_type'   => 'image/png',
            'src'         => 'https://media.changemyworldnow.com/f/70116569037',
            'name'        => 'img_animals_sprite.png',
            'can_overlap' => false,
        ];

        $this->assertEquals(
            $expected,
            $media->getArrayCopy(),
            'Did not extract media_id correctly from json'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnCollectionWithFolder()
    {
        $this->adapter->setResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.multiple.import.response'))
        );

        $media = $this->mediaService->importMediaData('70116569037');

        $this->assertEquals(
            Request::fromString(file_get_contents(__DIR__ . '/_response/valid.import.request'))->toString(),
            $this->client->getRequest()->toString()
        );

        $this->assertInstanceOf(MediaCollection::class, $media);
        $expected = [
            'media_id'    => '7302958933',
            'asset_type'  => 'folder',
            'check'       => [
                'type'  => null,
                'value' => null,
            ],
            'mime_type'   => null,
            'src'         => null,
            'name'        => 'Content',
            'can_overlap' => false,
        ];

        $iterator = $media->getIterator();
        $this->assertEquals(2, count($iterator));
        $iterator->rewind();
        $this->assertEquals(
            $expected,
            $iterator->current()->getArrayCopy(),
            'Did not extract media_id correctly from json'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenResourceIsNotFound()
    {
        $this->setExpectedException(NotFoundException::class);
        $this->adapter->setResponse(Response::fromString("HTTP/1.1 404 Not Found\r\n\r\n"));
        $this->mediaService->importMediaData('some-bad-id');
    }

    /**
     * @test
     */
    public function testItShouldWriteFile()
    {
        $this->adapter->setResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.import.response'))
        );

        $this->adapter->addResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.download.response'))
        );

        $this->assertTrue(
            $this->mediaService->importFile(
                '70116569037',
                realpath(getcwd() . '/../tmp')
            ),
            'Service did not return truth for download'
        );

        $this->assertTrue(
            file_exists(realpath(getcwd() . '/../tmp/70116569037')),
            'File was not downloaded to correct location'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionOnBadChecksum()
    {
        $this->adapter->setResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/bad-check.import.response'))
        );

        $this->adapter->addResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.download.response'))
        );

        try {
            $this->mediaService->importFile(
                '70116569037',
                realpath(getcwd() . '/../tmp')
            );

            $this->fail('MediaService::importFile did not throw required exception');
        } catch (InvalidResponseException $invalid) {
            // @codingStandardsIgnoreStart
            $this->assertEquals(
                'Invalid checksum: da39a3ee5e6b4b0d3255bfef95601890afd80709 expected da39a3ee5e6b4b0d3255bfef95601890afd807',
                $invalid->getMessage()
            );
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionOnBadContentType()
    {
        $this->adapter->setResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/bad-content.import.response'))
        );

        $this->adapter->addResponse(
            Response::fromString(file_get_contents(__DIR__ . '/_response/valid.download.response'))
        );

        try {
            $this->mediaService->importFile(
                '70116569037',
                realpath(getcwd() . '/../tmp')
            );

            $this->fail('MediaService::importFile did not throw required exception');
        } catch (InvalidResponseException $invalid) {
            $this->assertEquals(
                'Incorrect content type: image/png expected image/jpg',
                $invalid->getMessage()
            );
        }
    }
}
