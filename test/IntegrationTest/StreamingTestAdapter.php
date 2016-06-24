<?php

namespace IntegrationTest;

use Zend\Http\Client\Adapter\StreamInterface;
use Zend\Http\Client\Adapter\Test;

/**
 * Class StreamingTestAdapter
 */
class StreamingTestAdapter extends Test implements StreamInterface
{
    /**
     * @var string
     */
    protected $outputStream;

    /**
     * Set output stream
     *
     * This function sets output stream where the result will be stored.
     *
     * @param string $stream Stream to write the output to
     *
     */
    public function setOutputStream($stream)
    {
        $this->outputStream = $stream;
    }

    /**
     * This is a test it would be nice to get the stream back
     *
     * @return string
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }
}
