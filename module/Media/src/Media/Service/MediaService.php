<?php

namespace Media\Service;

use Application\Exception\NotFoundException;
use Media\InvalidResponseException;
use Media\Media;
use Media\MediaCollection;
use Media\MediaInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Header\ContentType;
use Zend\Json\Json;

/**
 * Class MediaService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MediaService implements MediaServiceInterface
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $baseUrl = 'https://media.changemyworldnow.com/';

    /**
     * MediaService constructor.
     *
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        // We clone to ensure that we are working with a fresh http client
        // Don't want any gunk from calls to other services interfering with
        // we're trying to do here
        $this->httpClient = clone $client;
        $this->httpClient->setUri($this->baseUrl);
    }

    /**
     * @param $base
     * @return $this
     */
    public function setBaseUrl($base)
    {
        $this->baseUrl = $base;
        $this->httpClient->setUri($this->baseUrl);
        return $this;
    }

    /**
     * Imports information about an asset from the media server
     *
     * @param $mediaId
     * @param $prototype
     * @throws NotFoundException
     * @return MediaInterface|MediaCollection
     */
    public function importMediaData($mediaId, $prototype = null)
    {
        $this->httpClient->getUri()->setPath('/a/' . $mediaId);
        $this->httpClient->send();

        if ($this->httpClient->getResponse()->getStatusCode() === 404) {
            throw new NotFoundException();
        }

        return $this->parseJsonFromRequest($prototype);
    }

    /**
     * Imports a file to the specified path
     *
     * This will save the file locally and check that the file is valid
     *
     * @param $mediaId
     * @param $savePath
     * @return bool
     * @throws \Exception
     */
    public function importFile($mediaId, $savePath = null)
    {
        $savePath  = null === $savePath ? ini_get('sys_temp_dir') : $savePath;
        $savePath  = realpath($savePath . DIRECTORY_SEPARATOR);

        $saveFile  = $savePath . DIRECTORY_SEPARATOR . $mediaId;
        try {
            $mediaData = $this->importMediaData($mediaId);

            if ($mediaData instanceof MediaCollection) {
                throw new \RuntimeException('Can only import single files');
            }

            $this->httpClient->getUri()->setPath('/f/' . $mediaId);
            $this->httpClient->setStream($saveFile);
            $this->httpClient->send();

            /** @var ContentType $contentType */
            $contentType = $this->httpClient->getResponse()->getHeaders()->get('Content-Type');

            if ($contentType === false || !$contentType->match($mediaData->getMimeType())) {
                $contentType = $contentType !== false ? $contentType->getFieldValue() : 'NOT_SET ';
                throw new InvalidResponseException(
                    sprintf('Incorrect content type: %s expected %s', $contentType, $mediaData->getMimeType())
                );
            }

            $this->validateFileChecksum($mediaData, $saveFile);
        } catch (\Exception $invalidResponse) {
            // remove the local file on exceptions
            @unlink($savePath);
            throw $invalidResponse;
        }

        return true;
    }

    /**
     * Checks the file is the correct file coming from the media server
     *
     * @param MediaInterface $media
     * @param $file
     * @return bool
     */
    protected function validateFileChecksum(MediaInterface $media, $file)
    {
        // @codeCoverageIgnoreStart
        if (!file_exists($file)) {
            throw new InvalidResponseException('Invalid File');
        }
        // @@codeCoverageIgnoreEnd

        switch ($media->getCheckType()) {
            case 'sha1':
                $hash = sha1_file($file);
                break;

            case 'md5':
                $hash = md5_file($file);
                break;

            default:
                throw new InvalidResponseException('Invalid checksum option: ' . $media->getCheckType());
        }

        if ($hash !== $media->getCheckValue()) {
            throw new InvalidResponseException(
                sprintf('Invalid checksum: %s expected %s', $hash, $media->getCheckValue())
            );
        }
    }

    /**
     * @param $prototype
     *
     * @return Media|MediaCollection|MediaInterface
     */
    protected function parseJsonFromRequest($prototype)
    {
        if ($this->httpClient->getResponse()->getStatusCode() !== 200) {
            throw new InvalidResponseException('Invalid Response Code');
        }

        try {
            $json = Json::decode($this->httpClient->getResponse()->getBody(), Json::TYPE_ARRAY);
        } catch (\Exception $jsonDecodeException) {
            throw new InvalidResponseException('Unable to parse Body: ' . $jsonDecodeException->getMessage());
        }

        $media   = $prototype instanceof MediaInterface ? $prototype : new Media();
        if ($json['type'] === 'file') {
            $media->exchangeArray($json);
            return $media;
        }

        $collection = new MediaCollection();
        foreach ($json['items'] as $mediaItem) {
            $media = clone $media;
            $media->exchangeArray($mediaItem);
            $collection->offsetSet($media->getMediaId(), $media);
        }

        return $collection;
    }
}
