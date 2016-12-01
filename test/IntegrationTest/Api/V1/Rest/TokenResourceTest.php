<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test TokenResourceTest
 *
 * @group Token
 * @group API
 * @group User
 * @group IntegrationTest
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TokenResourceTest extends TestCase
{
    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/token.dataset.php');
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     *
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckChangePasswordException($user, $url, $method = 'GET', $params = [])
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException($url, $method, $params);
    }

    /**
     * @test
     * @ticket CORE-681
     */
    public function testItShouldReturnDefaultHalLinksWhenNotLoggedIn()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');

            return;
        }

        $this->assertArrayHasKey('_links', $decoded);

        $links = $decoded['_links'];
        $this->assertArrayHasKey('login', $links);
        $this->assertArrayHasKey('logout', $links);
        $this->assertArrayHasKey('forgot', $links);

        $this->assertCount(3, $links);
    }

    /**
     * @test
     * @ticket       CORE-681
     * @ticket       CORE-1184
     * @ticket       CORE-1233
     * @dataProvider loginHalLinksDataProvider
     */
    public function testItShouldBuildCorrectEndpointsForMe($user, $links, $expectedScope)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');

            return;
        }

        $this->assertArrayHasKey('_links', $decoded, 'No hal links returned on me');
        $this->assertArrayHasKey('scope', $decoded, 'No Scope returned on me');

        sort($links);
        $actualLinks = array_keys($decoded['_links']);
        sort($actualLinks);
        $this->assertEquals($links, $actualLinks);

        $this->assertEquals($expectedScope, $decoded['scope'], 'Incorrect scope for ME');
    }

    /**
     * @return array
     */
    public function loginHalLinksDataProvider()
    {
        return [
            'Super User'      => [
                'user'  => 'super_user',
                'links' => [
                    'flip',
                    'games',
                    'group_class',
                    'group_school',
                    'org_district',
                    'password',
                    'profile',
                    'self',
                    'user',
                    'user_image',
                    'save_game',
                    'flags',
                ],
                'scope' => -1,
            ],
            'Principal'       => [
                'user'  => 'principal',
                'links' => [
                    'flip',
                    'games',
                    'group_school',
                    'group_class',
                    'org_district',
                    'password',
                    'profile',
                    'self',
                    'user',
                    'user_image',
                    'save_game',
                    'flags',
                ],
                'scope' => 2,
            ],
            'English Teacher' => [
                'user'  => 'english_teacher',
                'links' => [
                    'flip',
                    'games',
                    'group_school',
                    'group_class',
                    'org_district',
                    'password',
                    'profile',
                    'self',
                    'user',
                    'user_image',
                    'save_game',
                    'flags',
                ],
                'scope' => 2,
            ],
            'English Student' => [
                'user'  => 'english_student',
                'links' => [
                    'feed',
                    'flip',
                    'friend',
                    'games',
                    'group_class',
                    'password',
                    'profile',
                    'self',
                    'skribbles',
                    'suggested_friends',
                    'user',
                    'user_flip',
                    'user_image',
                    'user_name',
                    'save_game',
                    'flags',
                ],
                'scope' => 2,
            ],
        ];
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/',
            ],
        ];
    }
}
