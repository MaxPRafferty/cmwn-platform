<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

/**
 * Integration tests for user cards resource
 */
class UserCardsResourceTest extends AbstractApigilityTestCase
{
    /**
     * @return ArrayDataSet
     */
    public function getDataSet(): ArrayDataSet
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/default.dataset.php');
    }

    /**
     * @test
     */
    public function testItShouldGeneratePDF()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/group/school/cards');
        $this->assertResponseStatusCode(200);
    }
}
