<?php

namespace IntegrationTest\Api\V1\Rest;

use Api\V1\Rest\Token\TokenResource;
use IntegrationTest\IntegrationTest as TestCase;
use Zend\Json\Json;

/**
 * Tests the hal links for various users on the token resource
 *
 * @group Hal
 */
class TokenResourceTest extends TestCase
{
    /**
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/token.dataset.php');
    }

    /**
     * @inheritDoc
     */
    protected function getControllerNameForTest(): string
    {
        return 'api\v1\rest\token\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.token';
    }

    /**
     * @param array $expectedLinks
     *
     * @return array
     */
    protected function checkLinks(array $expectedLinks)
    {
        try {
            $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');

            return [];
        }

        $actualLinks = [];
        foreach (($body['_links'] ?? []) as $rel => $link) {
            $actualLinks[$rel] = $link['href']?? null;
        }

        $this->assertEquals(
            $expectedLinks,
            $actualLinks,
            TokenResource::class . ' has invalid links for this route'
        );

        return $body;
    }

    /**
     * @test
     */
    public function testItShouldReturnDefaultHalLinksWhenNotLoggedIn()
    {
        $this->dispatchCall('/');

        $this->checkLinks([
            'login'  => 'http://api.test.com/login',
            'logout' => 'http://api.test.com/logout',
            'forgot' => 'http://api.test.com/forgot',
        ]);
    }

    /**
     * @test
     *
     * @param string $login
     * @param array $links
     * @param int $expectedScope
     *
     * @dataProvider loginHalLinksDataProvider
     */
    public function testItShouldBuildCorrectResponseForMe(
        string $login,
        array $links,
        int $expectedScope,
        string $friendStatus = null
    ) {
        $user = $this->dispatchAuthenticatedCall($login, '/');
        $body = $this->checkLinks($links);

        // Remove links and remove embedded
        unset($body['_links'], $body['_embedded']);

        $expectedBody = array_merge($user->getArrayCopy(), ['token' => 'foobar', 'scope' => $expectedScope]);
        if ($friendStatus !== null) {
            $expectedBody['friend_status'] = $friendStatus;
        } else {
            $this->assertArrayNotHasKey(
                'friend_status',
                $body,
                TokenResource::class . ' is including friend status for user that cant friend'
            );
        }
        $this->assertEquals(
            $body,
            $expectedBody,
            TokenResource::class . ' did not return the correct data for the user'
        );
    }

    /**
     * @return array
     */
    public function loginHalLinksDataProvider()
    {
        return [
            'Super User'      => [
                'user'          => 'super_user',
                'links'         => [
                    'self'          => 'http://api.test.com/user/super_user',
                    'feed'          => 'http://api.test.com/feed',
                    'flip'          => 'http://api.test.com/flip',
                    'games'         => 'http://api.test.com/game',
                    'games_deleted' => 'http://api.test.com/game?deleted=true',
                    'group'         => 'http://api.test.com/group',
                    'group_class'   => 'http://api.test.com/group?type=class',
                    'group_school'  => 'http://api.test.com/group?type=school',
                    'org'           => 'http://api.test.com/org',
                    'org_district'  => 'http://api.test.com/org?type=district',
                    'password'      => 'http://api.test.com/user/super_user/password',
                    'profile'       => 'http://api.test.com/user/super_user',
                    'user'          => 'http://api.test.com/user',
                    'user_image'    => 'http://api.test.com/user/super_user/image',
                    'user_flip'     => 'http://api.test.com/user/super_user/flip',
                    'super'         => 'http://api.test.com/super/super_user',
                    'flags'         => 'http://api.test.com/flag',
                    'sa_settings'   => 'http://api.test.com/sa/settings',
                    'user_feed'     => 'http://api.test.com/user/super_user/feed',
                    'address'       => 'http://api.test.com/address',
                ],
                'scope'         => -1,
                'friend_status' => null,
            ],
            'Principal'       => [
                'user'          => 'principal',
                'links'         => [
                    'self'         => 'http://api.test.com/user/principal',
                    'flip'         => 'http://api.test.com/flip',
                    'user'         => 'http://api.test.com/user',
                    'password'     => 'http://api.test.com/user/principal/password',
                    'user_feed'    => 'http://api.test.com/user/principal/feed',
                    'flags'        => 'http://api.test.com/flag',
                    'games'        => 'http://api.test.com/user/principal/game',
                    'user_flip'    => 'http://api.test.com/user/principal/flip',
                    'profile'      => 'http://api.test.com/user/principal',
                    'user_image'   => 'http://api.test.com/user/principal/image',
                    'group_class'  => 'http://api.test.com/group?type=class',
                    'group_school' => 'http://api.test.com/group?type=school',
                    'org_district' => 'http://api.test.com/org?type=district',
                ],
                'scope'         => 2,
                'friend_status' => null,
            ],
            'English Teacher' => [
                'user'          => 'english_teacher',
                'links'         => [
                    'self'         => 'http://api.test.com/user/english_teacher',
                    'flip'         => 'http://api.test.com/flip',
                    'user'         => 'http://api.test.com/user',
                    'password'     => 'http://api.test.com/user/english_teacher/password',
                    'user_feed'    => 'http://api.test.com/user/english_teacher/feed',
                    'flags'        => 'http://api.test.com/flag',
                    'games'        => 'http://api.test.com/user/english_teacher/game',
                    'user_flip'    => 'http://api.test.com/user/english_teacher/flip',
                    'profile'      => 'http://api.test.com/user/english_teacher',
                    'user_image'   => 'http://api.test.com/user/english_teacher/image',
                    'group_class'  => 'http://api.test.com/group?type=class',
                    'group_school' => 'http://api.test.com/group?type=school',
                    'org_district' => 'http://api.test.com/org?type=district',
                ],
                'scope'         => 2,
                'friend_status' => null,
            ],
            'English Student' => [
                'user'          => 'english_student',
                'links'         => [
                    'self'              => 'http://api.test.com/user/english_student',
                    'suggested_friends' => 'http://api.test.com/user/english_student/suggest',
                    'friend'            => 'http://api.test.com/user/english_student/friend',
                    'skribbles'         => 'http://api.test.com/user/english_student/skribble',
                    'flip'              => 'http://api.test.com/flip',
                    'user'              => 'http://api.test.com/user',
                    'password'          => 'http://api.test.com/user/english_student/password',
                    'user_feed'         => 'http://api.test.com/user/english_student/feed',
                    'flags'             => 'http://api.test.com/flag',
                    'save_game'         => 'http://api.test.com/user/english_student/save/{game_id}',
                    'games'             => 'http://api.test.com/user/english_student/game',
                    'user_flip'         => 'http://api.test.com/user/english_student/flip',
                    'user_name'         => 'http://api.test.com/user-name',
                    'profile'           => 'http://api.test.com/user/english_student',
                    'user_image'        => 'http://api.test.com/user/english_student/image',
                    'group_class'       => 'http://api.test.com/group?type=class',
                ],
                'scope'         => 2,
                'friend_status' => 'CANT_FRIEND',
            ],
        ];
    }
}
