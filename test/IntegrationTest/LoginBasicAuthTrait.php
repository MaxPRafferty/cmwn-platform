<?php

namespace IntegrationTest;

use Zend\Http\Header\Authorization;
use Zend\Http\Request;

/**
 * Trait LoginBasicAuthTrait
 */
trait LoginBasicAuthTrait
{
    /**
     * Creates a basic auth header auth line
     *
     * @param Request $request
     */
    public function loginBasicAuth(Request $request)
    {
        $header = new Authorization('Basic ' . base64_encode('janeway:janewaypi110'));
        $request->getHeaders()->addHeader($header);
    }
}
