<?php

namespace IntegrationTest;

use Security\Authentication\AuthAdapter;
use Security\Guard\CsrfGuard;
use Zend\Authentication\AuthenticationService;
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
     * @return AuthenticationService
     */
    protected function getAuthService()
    {
        return TestHelper::getServiceManager()->get(AuthenticationService::class);
    }

    /**
     * @after
     */
    public function logOutUser()
    {
        $this->getAuthService()->clearIdentity();
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
     * Logs in a user (from the test DB)
     *
     * @param $userName
     */
    public function logInUser($userName, $forceRole = null)
    {
        /** @var AuthAdapter $adapter */
        $adapter = TestHelper::getServiceManager()->get(AuthAdapter::class);

        $adapter->setPassword('business');
        $adapter->setUserIdentifier($userName);

        $this->getAuthService()->authenticate($adapter);
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     */
    public function dispatch($url, $method = 'GET', $params = [])
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
