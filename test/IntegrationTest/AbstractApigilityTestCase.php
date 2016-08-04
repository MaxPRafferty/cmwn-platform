<?php

namespace IntegrationTest;

use Security\Guard\CsrfGuard;
use Zend\Json\Json;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as TestCase;
use ZF\ContentNegotiation\Request;
use \PHPUnit_Extensions_Database_TestCase_Trait as DbTestCaseTrait;

/**
 * Class AbstractApigilityTestCase
 *
 * @method Request getRequest()
 */
abstract class AbstractApigilityTestCase extends TestCase
{
    use DbTestCaseTrait;
    use DbUnitConnectionTrait;
    use LoginUserTrait;

    /**
     * @var string The accept type for the request
     */
    protected $acceptType = 'application/json';

    /**
     * Sets up the full application
     */
    public function setUp()
    {
        $this->setApplicationConfig(
            TestHelper::getApplicationConfig()
        );

        parent::setUp();

        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    /**
     * Performs operation returned by getTearDownOperation().
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onTearDown();

        /**
         * Destroy the tester after the test is run to keep DB connections
         * from piling up.
         */
        $this->databaseTester = null;
    }

    /**
     * Sets the request to be a valid CSRF token
     */
    public function injectValidCsrfToken()
    {
        /** @var CsrfGuard $xsrfGuard */
        $xsrfGuard = TestHelper::getServiceManager()->get(CsrfGuard::class);
        $xsrfGuard->getSession()->offsetSet('hash', 'foobar');

        $this->getRequest()
            ->getHeaders()
            ->addHeaderLine('X-CSRF: foobar');
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @param bool $isXmlHttpRequest
     */
    public function dispatch($url, $method = 'GET', $params = [], $isXmlHttpRequest = false)
    {
        $this->url($url, $method, $params);

        if (!empty($params)) {
            $this->getRequest()->getHeaders()->addHeaderLine('Content-Type: application/json');
            $params = !empty($params) ? Json::encode($params) : $params;
            $this->getRequest()->setContent($params);
        }

        $this->getRequest()
            ->getHeaders()
            ->addHeaderLine('Accept: ' . $this->acceptType)
            ->addHeaderLine('Origin: https://unit-test.changemyworldnow.com');

        $this->getApplication()->run();
        $this->assertCorrectCorsHeaders();
    }

    /**
     * Check for change password exception
     *
     * @param string $url
     * @param string $method
     * @param array $params
     */
    public function assertChangePasswordException($url, $method = 'GET', $params = [])
    {
        $this->dispatch($url, $method, $params);
        $this->assertResponseStatusCode(401);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals(
            'RESET_PASSWORD',
            $body['detail'],
            sprintf(
                'Failed asserting that change password exception is thrown. Actual exception is %s',
                $this->getResponse()->getContent()
            )
        );
    }

    /**
     * Assert response status code
     *
     * @param int $code
     */
    public function assertResponseStatusCode($code)
    {
        $match = $this->getResponseStatusCode();

        $this->assertEquals(
            $code,
            $match,
            sprintf(
                'Failed asserting response code "%s", actual status code is "%s"'
                . PHP_EOL . 'RESPONSE BODY:' . PHP_EOL .  '%s',
                $code,
                $match,
                $this->getResponse()->getContent()
            )
        );
    }

    /**
     * Helps check that all the CORS headers are set
     */
    public function assertCorrectCorsHeaders()
    {
        $this->assertResponseHeaderContains('Access-Control-Allow-Credentials', 'true');
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', 'https://unit-test.changemyworldnow.com');
        $this->assertResponseHeaderContains('Access-Control-Allow-Methods', 'GET, POST, PATCH, OPTIONS, PUT, DELETE');
        $this->assertResponseHeaderContains('Access-Control-Allow-Headers', 'Origin, Content-Type, X-CSRF');
        $this->assertResponseHeaderContains('Access-Control-Max-Age', '28800');
    }
}
