<?php

namespace SecurityTest\Guard;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\Guard\OriginGuard;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Header\Origin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\Parameters;

/**
 * Test OriginGuardTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OriginGuardTest extends TestCase
{
    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @before
     */
    public function setUpRequest()
    {
        $this->request = new Request();
        $parameters    = new Parameters();
        $parameters->set('HTTP_HOST', 'test.changemyworldnow.com');
        $this->request->setServer($parameters);
    }

    /**
     * @before
     */
    public function setUpResponse()
    {
        $this->response = new Response();
    }

    /**
     * @before
     */
    public function setUpEvent()
    {
        $this->event = new MvcEvent();
        $this->event->setResponse($this->response);
        $this->event->setRequest($this->request);
    }

    /**
     * @test
     * @dataProvider validCMWNDomains
     */
    public function testItShouldSetMatchingCORSForAnyCMWNSubDomain($domain)
    {
        $origin = new Origin($domain);
        $this->request->getHeaders()->addHeader($origin);

        $guard = new OriginGuard();
        $guard->attachCors($this->event);

        $acacHeader = $this->response->getHeaders()->get('Access-Control-Allow-Credentials');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acacHeader,
            'Access-Control-Allow-Credentials header not set'
        );

        $this->assertEquals(
            'true',
            $acacHeader->getFieldValue(),
            'Access-Control-Allow-Credentials header not set to: true'
        );

        $acaoHeader = $this->response->getHeaders()->get('Access-Control-Allow-Origin');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acaoHeader,
            'Access-Control-Allow-Origin header not set'
        );

        $this->assertEquals(
            $domain,
            $acaoHeader->getFieldValue(),
            'Access-Control-Allow-Origin header not set to: ' . $domain
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Methods');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Methods header not set'
        );

        $this->assertEquals(
            'GET, POST, PATCH, OPTIONS, PUT, DELETE',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Methods header not set to: GET, POST, PATCH, OPTIONS, PUT, DELETE'
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Headers');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Headers header not set'
        );

        $this->assertEquals(
            'Origin, Content-Type, X-CSRF',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Headers header not set to: Origin, Content-Type, X-CSRF'
        );

        $acmaHeader = $this->response->getHeaders()->get('Access-Control-Max-Age');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acmaHeader,
            'Access-Control-Max-Age header not set'
        );

        $this->assertEquals(
            '28800',
            $acmaHeader->getFieldValue(),
            'Access-Control-Max-Age header not set to: Origin, Content-Type, X-CSRF'
        );
    }

    /**
     * @test
     */
    public function testItShouldSetSeverHostWhenOriginDoesNotMatch()
    {
        $origin = new Origin('http://www.manchuck.com');
        $this->request->getHeaders()->addHeader($origin);

        $guard = new OriginGuard();
        $guard->attachCors($this->event);

        $acacHeader = $this->response->getHeaders()->get('Access-Control-Allow-Credentials');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acacHeader,
            'Access-Control-Allow-Credentials header not set'
        );

        $this->assertEquals(
            'true',
            $acacHeader->getFieldValue(),
            'Access-Control-Allow-Credentials header not set to: true'
        );

        $acaoHeader = $this->response->getHeaders()->get('Access-Control-Allow-Origin');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acaoHeader,
            'Access-Control-Allow-Origin header not set'
        );

        $this->assertEquals(
            'https://test.changemyworldnow.com',
            $acaoHeader->getFieldValue(),
            'Access-Control-Allow-Origin header not set to: https://test.changemyworldnow.com'
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Methods');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Methods header not set'
        );

        $this->assertEquals(
            'GET, POST, PATCH, OPTIONS, PUT, DELETE',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Methods header not set to: GET, POST, PATCH, OPTIONS, PUT, DELETE'
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Headers');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Headers header not set'
        );

        $this->assertEquals(
            'Origin, Content-Type, X-CSRF',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Headers header not set to: Origin, Content-Type, X-CSRF'
        );

        $acmaHeader = $this->response->getHeaders()->get('Access-Control-Max-Age');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acmaHeader,
            'Access-Control-Max-Age header not set'
        );

        $this->assertEquals(
            '28800',
            $acmaHeader->getFieldValue(),
            'Access-Control-Max-Age header not set to: Origin, Content-Type, X-CSRF'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnBackSeverHostWhenNoOriginHeaderFound()
    {

        $guard = new OriginGuard();
        $guard->attachCors($this->event);

        $acacHeader = $this->response->getHeaders()->get('Access-Control-Allow-Credentials');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acacHeader,
            'Access-Control-Allow-Credentials header not set'
        );

        $this->assertEquals(
            'true',
            $acacHeader->getFieldValue(),
            'Access-Control-Allow-Credentials header not set to: true'
        );

        $acaoHeader = $this->response->getHeaders()->get('Access-Control-Allow-Origin');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acaoHeader,
            'Access-Control-Allow-Origin header not set'
        );

        $this->assertEquals(
            'https://test.changemyworldnow.com',
            $acaoHeader->getFieldValue(),
            'Access-Control-Allow-Origin header not set to: https://test.changemyworldnow.com'
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Methods');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Methods header not set'
        );

        $this->assertEquals(
            'GET, POST, PATCH, OPTIONS, PUT, DELETE',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Methods header not set to: GET, POST, PATCH, OPTIONS, PUT, DELETE'
        );

        $acamHeader = $this->response->getHeaders()->get('Access-Control-Allow-Headers');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acamHeader,
            'Access-Control-Allow-Headers header not set'
        );

        $this->assertEquals(
            'Origin, Content-Type, X-CSRF',
            $acamHeader->getFieldValue(),
            'Access-Control-Allow-Headers header not set to: Origin, Content-Type, X-CSRF'
        );

        $acmaHeader = $this->response->getHeaders()->get('Access-Control-Max-Age');

        $this->assertInstanceOf(
            HeaderInterface::class,
            $acmaHeader,
            'Access-Control-Max-Age header not set'
        );

        $this->assertEquals(
            '28800',
            $acmaHeader->getFieldValue(),
            'Access-Control-Max-Age header not set to: Origin, Content-Type, X-CSRF'
        );
    }

    /**
     * @return array
     */
    public function validCMWNDomains()
    {
        return [
            'Full Production' => [
                'domain' => 'https://www.changemyworldnow.com',
            ],

            'Relative Production' => [
                'domain' => 'https://changemyworldnow.com',
            ],

            'Api Production' => [
                'domain' => 'https://api.changemyworldnow.com',
            ],

            'Staging' => [
                'domain' => 'https://staging.changemyworldnow.com',
            ],

            'Api Staging' => [
                'domain' => 'https://api-staging.changemyworldnow.com',
            ],

            'Qa' => [
                'domain' => 'https://qa.changemyworldnow.com',
            ],

            'Api Qa' => [
                'domain' => 'https://api-dev.changemyworldnow.com',
            ],

            'Local' => [
                'domain' => 'https://local.changemyworldnow.com',
            ],

            'Api Local' => [
                'domain' => 'https://api-local.changemyworldnow.com',
            ],

            'Api Unit Test' => [
                'domain' => 'https://unit-test.changemyworldnow.com',
            ],
        ];
    }
}
