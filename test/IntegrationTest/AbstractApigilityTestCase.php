<?php

namespace IntegrationTest;

use Security\Authentication\AuthAdapter;
use Security\Guard\CsrfGuard;
use Security\SecurityUser;
use User\Service\UserServiceInterface;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as TestCase;
use ZF\ContentNegotiation\Request;
use \PHPUnit_Extensions_Database_TestCase_Trait as DbTestCaseTrait;

/**
 * Class AbstractApigilityTestCase
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
     * @before
     */
    public function setUpRequestForApigility()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('Accept: ' . $this->acceptType);
    }

    /**
     * Sets the request to be a valid CSRF token
     */
    public function injectValidCsrfToken()
    {
        /** @var CsrfGuard $xsrfGuard */
        $xsrfGuard = TestHelper::getServiceManager()->get(CsrfGuard::class);
        $xsrfGuard->getSession()->offsetSet('hash', 'foobar');

        /** @var Request $request */
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X-CSRF: foobar');
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
}
