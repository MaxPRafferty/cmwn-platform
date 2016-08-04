<?php

namespace IntegrationTest;

use Zend\Authentication\Adapter\Http\FileResolver;
use Zend\Authentication\Adapter\Http\ResolverInterface;
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
        /** @var FileResolver $resolver */
        $resolver = TestHelper::getServiceManager()->get(ResolverInterface::class);
        $resolver->setFile(realpath(__DIR__ . '/_files/.htpasswd-test'));

        $header = new Authorization('Basic ' . base64_encode('janeway:janewaypi110'));
        $request->getHeaders()->addHeader($header);
    }
}
